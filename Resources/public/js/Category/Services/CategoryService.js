//no import of Angular stuff ($http, $q…)
export default class CategoryService {
    constructor($http, $q) {
        //declaration of variables
        this.$http      = $http
        this.$q         = $q
        this._tree      = CategoryService._getGlobal('simupollTree')
        this._sid       = CategoryService._getGlobal('simupollSid')
    }

    getTree () {
      return this._tree
    }

    getSid () {
      return this._sid
    }

    deleteCategory (category, onFail) {
      const url = Routing.generate('simupoll_delete_category', {
        cid: category.id
      })

      this._deleteCategory(category)

      this.$http
        .delete(url)
        .then(null, () => {
          this._tree.push(category)
          onFail()
        })
    }

    _deleteCategory (category) {
      this._tree.splice(this._tree.indexOf(category), 1)
    }

    //defined in template script
    static _getGlobal (name) {
      if (typeof window[name] === 'undefined') {
        throw new Error(
          `Expected ${name} to be exposed in a window.${name} variable`
        )
      }
      return window[name]
    }
}
