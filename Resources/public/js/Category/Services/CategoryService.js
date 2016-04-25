//no import of Angular stuff ($http, $q…)
export default class CategoryService {
    constructor($http, $q) {
        //declaration of variables
        this.$http      = $http
        this.$q         = $q
        this._tree      = CategoryService._getGlobal('simupollTree')
        this._sid       = CategoryService._getGlobal('simupollSid')
    }

    getCategories() {
        return this.$http.get('/categories/'+this._sid)
    }

    getTree () {
      return this._tree
    }

    getSid () {
      return this._sid
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
