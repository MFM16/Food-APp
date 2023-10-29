function data() {
  function getThemeFromLocalStorage() {
    // if user already changed the theme, use it
    if (window.localStorage.getItem('dark')) {
      return JSON.parse(window.localStorage.getItem('dark'))
    }

    // else return their preferences
    return (
      !!window.matchMedia &&
      window.matchMedia('(prefers-color-scheme: dark)').matches
    )
  }

  function setThemeToLocalStorage(value) {
    window.localStorage.setItem('dark', value)
  }

  return {
    dark: getThemeFromLocalStorage(),
    toggleTheme() {
      this.dark = !this.dark
      setThemeToLocalStorage(this.dark)
    },
    isSideMenuOpen: false,
    toggleSideMenu() {
      this.isSideMenuOpen = !this.isSideMenuOpen
    },
    closeSideMenu() {
      this.isSideMenuOpen = false
    },
    isNotificationsMenuOpen: false,
    toggleNotificationsMenu() {
      this.isNotificationsMenuOpen = !this.isNotificationsMenuOpen
    },
    closeNotificationsMenu() {
      this.isNotificationsMenuOpen = false
    },
    isProfileMenuOpen: false,
    toggleProfileMenu() {
      this.isProfileMenuOpen = !this.isProfileMenuOpen
    },
    closeProfileMenu() {
      this.isProfileMenuOpen = false
    },
    isPagesMenuOpen: false,
    togglePagesMenu() {
      this.isPagesMenuOpen = !this.isPagesMenuOpen
    },
    // Modal
    isModalOpen: false,
    trapCleanup: null,
    openModal(headerId = null, modalTitle, id, bgId = null, bgIdHide = null) {
      this.isModalOpen = true
      this.trapCleanup = focusTrap(document.querySelector(`#${id}`))
      if (headerId == null) {
        document.getElementById('modal-header').innerHTML = modalTitle
      } else {
        document.getElementById(`modal-header-${headerId}`).innerHTML = modalTitle
      }

      if (bgIdHide != null) {
        document.getElementById(`${bgIdHide}`).classList.add('hidden')
        document.getElementById(`${bgId}`).classList.remove('hidden')
      }
    },
    closeModal() {
      this.isModalOpen = false
      this.trapCleanup()
      document.getElementById('form').reset()
      if (document.getElementById('process-state')) {
        document.getElementById('process-state').value = 'add'
      }
    },
  }
}
