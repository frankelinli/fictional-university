import $ from 'jquery'

class Search {
  // 属性
  constructor() {
    this.addSearchHTML()

    this.openButton = $('.js-search-trigger')
    this.closeButton = $('.search-overlay__close')
    this.searchOverlay = $('.search-overlay')
    this.inputField = $('#search-term')
    this.resultsContainer = $('#search-overlay__results')

    this.overlayIsOpen = false
    this.inputTimer = null
    this.loading = false
    this.prevInuptValue = ''

    // 监听事件
    this.events()
  }

  // 事件
  events() {
    this.openButton.on('click', this.openOverlay.bind(this))
    this.closeButton.on('click', this.closeOverlay.bind(this))
    $(document).on('keydown', this.handelKeyborad.bind(this))
    this.inputField.on('input', this.handelInput.bind(this))
  }

  // 方法
  openOverlay() {
    this.overlayIsOpen = true
    this.searchOverlay.addClass('search-overlay--active')
    $('body').addClass('body-no-scroll')
    // CSS 渐显动画为 0.3 秒
    setTimeout(() => this.inputField.trigger('focus'), 301)
  }

  closeOverlay() {
    this.overlayIsOpen = false
    this.searchOverlay.removeClass('search-overlay--active')
    $('body').removeClass('body-no-scroll')

    // 关闭搜索遮罩层后清空输入框和搜索结果并使输入框失去焦点
    this.inputField.val('')
    this.inputField.trigger('blur')
    this.prevInuptValue = ''
    this.resultsContainer.html('')
  }

  handelKeyborad(e) {
    // 按下 Ctrl + k，且用户不在其他输入框输入时，不响应，对于单键快捷键有很好的效果
    if (e.keyCode === 75 && e.ctrlKey && !['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
      // 遮罩层是关闭的
      if (!this.overlayIsOpen) {
        this.openOverlay()
      }

      e.preventDefault()
      // 按下 Esc，且遮罩层是打开的
    } else if (this.overlayIsOpen && e.keyCode === 27) {
      this.closeOverlay()
      e.preventDefault()
    }
  }

  handelInput() {
    // 如果输入框的值和上一次的值不一样，则继续执行
    if (this.inputField.val() !== this.prevInuptValue) {
      // 如果有定时器，则清除定时器
      if (this.inputTimer) {
        clearTimeout(this.inputTimer)
      }

      // 如果加载动画没有显示，则显示加载动画
      if (!this.loading) {
        this.loading = true
        this.resultsContainer.html('<div class="spinner-loader"></div>')
      }

      // 如果输入框不为空，则执行定时器延迟搜索
      if (this.inputField.val().trim() !== '') {
        this.inputTimer = setTimeout(() => {
          this.inputTimer = null
          this.loading = false
          this.getResults.apply(this)
        }, 750)

        // 保存输入框的值
        this.prevInuptValue = this.inputField.val()

        // 否则关闭加载动画，清空结果容器
      } else {
        this.loading = false
        this.resultsContainer.html('')
      }
    }
  }

  getResults() {
    $.getJSON(universityData.siteUrl + '/wp-json/university/v1/search?s=' + this.inputField.val(), results => {
      const htmlStr = `
        <div class="row">
          <div class="one-third">
            <h2 class="search-overlay__section-title">一般信息</h2>
            ${results.generalInfo.length ? '<ul class="link-list min-list">' : '<p>没有找到相关信息</p>'}
              ${results.generalInfo
                .map(
                  item => `
                  <li>
                    <a href="${item.permalink}">${item.title}</a>
                    ${item.postType === 'post' ? `由 ${item.authorName} 发表` : ''}
                  </li>
                `
                )
                .join('')}
            ${results.generalInfo.length ? '</ul>' : ''}
          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-title">学科</h2>
            ${
              results.programs.length
                ? '<ul class="link-list min-list">'
                : `<p>没有找到相关学科 <a href="${universityData.siteUrl}/programs/">查看所有学科</a></p>`
            }
              ${results.programs
                .map(
                  item => `
                  <li>
                    <a href="${item.permalink}">${item.title}</a>
                  </li>
                `
                )
                .join('')}
            ${results.programs.length ? '</ul>' : ''}

            <h2 class="search-overlay__section-title">教授</h2>
            ${results.professors.length ? '<ul class="professor-cards">' : `<p>没有找到相关教授</p>`}
              ${results.professors
                .map(
                  item => `
                  <li class="professor-card__list-item">
                    <a class="professor-card" href="${item.permalink}">
                      <img class="professor-card__image" src="${item.image}">
                      <span class="professor-card__name">${item.title}</span>
                    </a>
                  </li>
                `
                )
                .join('')}
            ${results.professors.length ? '</ul>' : ''}
          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-title">校区</h2>
            ${
              results.campuses.length
                ? '<ul class="link-list min-list">'
                : `<p>没有找到相关校区 <a href="${universityData.siteUrl}/campuses/">查看所有校区</a></p>`
            }
              ${results.campuses
                .map(
                  item => `
                  <li>
                    <a href="${item.permalink}">${item.title}</a>
                  </li>
                `
                )
                .join('')}
            ${results.campuses.length ? '</ul>' : ''}

            <h2 class="search-overlay__section-title">活动</h2>
            ${
              results.events.length
                ? ''
                : `<p>没有找到相关活动 <a href="${universityData.siteUrl}/events/">查看所有活动</a></p>`
            }
              ${results.events
                .map(
                  item => `
                  <div class="event-summary">
                    <a class="event-summary__date t-center" href="${item.permalink}">
                      <span class="event-summary__month">
                        ${parseInt(item.date.substring(4, 6))}月
                      </span>
                      <span class="event-summary__day">${parseInt(item.date.substring(6, 8))}</span>
                    </a>
                    <div class="event-summary__content">
                      <h5 class="event-summary__title headline headline--tiny">
                        <a href="${item.permalink}">${item.title}</a>
                      </h5>
                      <p>
                        ${item.excerpt}
                        <a href="${item.permalink}" class="nu gray">了解更多</a>
                      </p>
                    </div>
                  </div>
                `
                )
                .join('')}
          </div>
        </div>
      `
      this.resultsContainer.html(htmlStr)
      this.loading = false
    }).fail(e => {
      console.log(e)
      this.resultsContainer.html('<p>意外错误，请再试一次。</p>')
      this.loading = false
    })
  }

  addSearchHTML() {
    $('body').append(`
      <div class="search-overlay">
        <div class="serach--overlay__top">
          <div class="container">
            <i class="fa fa-search search-overlay__icon" aria--hidden="true"></i>
            <input
              type="text"
              name=""
              id="search-term"
              class="search-term"
              placeholder="您正在寻找什么？"
            >
            <i class="fa fa-window-close search-overlay__close" aria--hidden="true"></i>
          </div>
        </div>

        <div class="container">
          <div id="search-overlay__results"></div>
        </div>
      </div>
    `)
  }
}

export default Search
