//no import of Angular stuff ($http, $q…)
export default class SimupollCreatorService {
    constructor($http, $q) {
        //declaration of variables
        this.$http      = $http
        this.$q         = $q
         this._simupoll = SimupollCreatorService._getGlobal('simupollData')
         this._sid      = SimupollCreatorService._getGlobal('simupollSid')
    }

    getSimupoll () {
      return this._simupoll
    }

    getSid () {
      return this._sid
    }

    deleteProposition(proposition, index) {
        const url = Routing.generate('simupoll_delete_proposition', {
          pid: proposition.id
        })

        this._deleteProposition(proposition)

        this.$http
          .delete(url)
          .then(null, () => {
            //this._periods.push(proposition)
            onFail()
        })
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
