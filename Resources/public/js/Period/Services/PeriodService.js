//no import of Angular stuff ($http, $q…)
export default class PeriodService {
    constructor($http, $q) {
        //declaration of variables
        this.$http      = $http
        this.$q         = $q
        this._periods   = PeriodService._getGlobal('simupollPeriods')
        this._sid       = PeriodService._getGlobal('simupollSid')
    }

    getPeriods () {
      return this._periods
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
