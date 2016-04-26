//no import of Angular stuff ($http, $q…)
export default class SimupollCreatorService {
    constructor($http, $q) {
        //declaration of variables
        this.$http      = $http
        this.$q         = $q
        // this._periods   = SimupollCreatorService._getGlobal('simupollPeriods')
        // this._sid       = SimupollCreatorService._getGlobal('simupollSid')
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
