//no import of Angular stuff ($http, $q…)
export default class SimupollCreatorService {
    constructor($http, $q) {
        //declaration of variables
        this.$http          = $http
        this.$q             = $q
        this._description   = SimupollCreatorService._getGlobal('simupollDescription')
        this._simupoll      = SimupollCreatorService._getGlobal('simupollData')
        this._sid           = SimupollCreatorService._getGlobal('simupollSid')
    }

    getSimupoll () {
      return this._simupoll
    }

    getSid () {
      return this._sid
    }

    getDescription () {
      return this._description
    }

    saveSimupoll(description, props, onFail) {
        const url = Routing.generate('simupoll_save_simupoll', {
          sid: this._sid
        })

        this.$http
          //pass variables to controller
          .post(url, { description: props.description, simupollquestions:props.simupollquestions })
          .then(
            response => {response.data},
            //and check if it's alright
            () => {
                onFail()
              }
          )
    }

    addQuestion(simupoll) {
        const nbQ = simupoll.length
        const newQ = {id:nbQ, 'title':'', 'propositions':[]}
        simupoll.push(newQ)
    }

    addProposition(simupoll, question) {
        const nbP = simupoll[simupoll.indexOf(question)].propositions.length
        const newP = {id:nbP, 'choice':'', 'mark':''}
        simupoll[simupoll.indexOf(question)].propositions.push(newP)
    }

    deleteQuestion(index, onFail) {
        //no need to delete simple proposition in db, because no need to create in db
        this._deleteQuestion(index)
    }

    _deleteQuestion (index) {
        this._simupoll.splice(index, 1)
    }

    deleteProposition(question, index, onFail) {
        //no need to delete simple proposition in db, because no need to create in db
        this._deleteProposition(question, index)
    }

    _deleteProposition (question, index) {
        this._simupoll[this._simupoll.indexOf(question)].propositions.splice(index, 1)
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
