import axios from 'axios'

class Search {
  // 1. 描述并创建/启动我们的对象
  constructor() {
    this.addSearchHTML()
    this.resultsDiv = document.querySelector('#search-overlay__results')
    this.openButton = document.querySelectorAll('.js-search-trigger')
    this.closeButton = document.querySelector('.search-overlay__close')
    this.searchOverlay = document.querySelector('.search-overlay')
    this.searchField = document.querySelector('#search-term')
    this.isOverlayOpen = false
    this.isSpinnerVisible = false
    this.previousValue
    this.typingTimer
    this.events()
  }

  // 2. 事件
  events() {
    this.openButton.forEach(el => {
      el.addEventListener('click', e => {
        e.preventDefault()
        this.openOverlay()
      })
    })

    this.closeButton.addEventListener('click', () => this.closeOverlay())
    document.addEventListener('keydown', e => this.keyPressDispatcher(e))
    this.searchField.addEventListener('keyup', () => this.typingLogic())
  }

  // 3. 方法（功能、动作...）
  typingLogic() {
    if (this.searchField.value != this.previousValue) {
      clearTimeout(this.typingTimer)

      if (this.searchField.value) {
        if (!this.isSpinnerVisible) {
          this.resultsDiv.innerHTML = '<div class="spinner-loader"></div>'
          this.isSpinnerVisible = true
        }
        this.typingTimer = setTimeout(this.getResults.bind(this), 750)
      } else {
        this.resultsDiv.innerHTML = ''
        this.isSpinnerVisible = false
      }
    }

    this.previousValue = this.searchField.value
  }

  async getResults() {
    try {
      const response = await axios.get(
        universityData.siteUrl + '/wp-json/university/v1/search?s=' + this.searchField.value
      )
      const results = response.data
      this.resultsDiv.innerHTML = `
        <div class="row">
          <div class="one-third">
            <h2 class="search-overlay__section-title">一般信息</h2>
            ${results.generalInfo.length ? '<ul class="link-list min-list">' : '<p>没有找到相关信息</p>'}
              ${results.generalInfo
                .map(
                  item =>
                    `<li><a href="${item.permalink}">${item.title}</a> ${
                      item.postType == 'post' ? `由 ${item.authorName} 发表` : ''
                    }</li>`
                )
                .join('')}
            ${results.generalInfo.length ? '</ul>' : ''}
          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-title">学科</h2>
            ${
              results.programs.length
                ? '<ul class="link-list min-list">'
                : `<p>没有找到相关学科 <a href="${universityData.siteUrl}/programs">查看所有学科</a></p>`
            }
              ${results.programs.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
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
                : `<p>没有找到相关校区 <a href="${universityData.siteUrl}/campuses">查看所有校区</a></p>`
            }
              ${results.campuses.map(item => `<li><a href="${item.permalink}">${item.title}</a></li>`).join('')}
            ${results.campuses.length ? '</ul>' : ''}

            <h2 class="search-overlay__section-title">活动</h2>
            ${
              results.events.length
                ? ''
                : `<p>没有找到相关活动 <a href="${universityData.siteUrl}/events">查看所有活动</a></p>`
            }
              ${results.events
                .map(
                  item => `
                <div class="event-summary">
                  <a class="event-summary__date t-center" href="${item.permalink}">
                    <span class="event-summary__month">${parseInt(item.date.substring(4, 6))}月</span>
                    <span class="event-summary__day">${parseInt(item.date.substring(6, 8))}</span>
                  </a>
                  <div class="event-summary__content">
                    <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${
                      item.title
                    }</a></h5>
                    <p>${item.excerpt} <a href="${item.permalink}" class="nu gray">了解更多</a></p>
                  </div>
                </div>
              `
                )
                .join('')}

          </div>
        </div>
      `
      this.isSpinnerVisible = false
    } catch (e) {
      console.log(e)
      this.resultsDiv.innerHTML = '<p>意外错误，请再试一次。</p>'
      this.loading = false
    }
  }

  keyPressDispatcher(e) {
    // 按下 Ctrl + k，且用户不在其他输入框输入时，不响应，对于单键快捷键有很好的效果
    if (e.keyCode === 75 && e.ctrlKey && !['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
      // 遮罩层是关闭的
      if (!this.isOverlayOpen) {
        this.openOverlay()
      }

      e.preventDefault()
      // 按下 Esc，且遮罩层是打开的
    } else if (e.keyCode == 27 && this.isOverlayOpen) {
      this.closeOverlay()
    }
  }

  openOverlay() {
    this.searchOverlay.classList.add('search-overlay--active')
    document.body.classList.add('body-no-scroll')
    this.searchField.value = ''
    setTimeout(() => this.searchField.focus(), 301)
    console.log('our open method just ran!')
    this.isOverlayOpen = true
    return false
  }

  closeOverlay() {
    this.searchOverlay.classList.remove('search-overlay--active')
    document.body.classList.remove('body-no-scroll')
    this.isOverlayOpen = false
  }

  addSearchHTML() {
    document.body.insertAdjacentHTML(
      'beforeend',
      `
      <div class="search-overlay">
        <div class="search-overlay__top">
          <div class="container">
            <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
            <input type="text" class="search-term" placeholder="您正在寻找什么？" id="search-term">
            <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
          </div>
        </div>
        
        <div class="container">
          <div id="search-overlay__results"></div>
        </div>

      </div>
    `
    )
  }
}

export default Search
