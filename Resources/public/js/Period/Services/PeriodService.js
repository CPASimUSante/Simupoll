//no import of Angular stuff ($http, $q…)
export default class PeriodService {
    constructor($http, $q) {
        //declaration of variables
        this.$http      = $http
        this.$q         = $q
        //from PeriodController:indexAction
        this._periods   = PeriodService._getGlobal('simupollPeriods')
        this._sid       = PeriodService._getGlobal('simupollSid')
    }

    getPeriods () {
      return this._periods
    }

    getSid () {
      return this._sid
    }

    addPeriod (props, period, onFail) {
      const result = { title: props.title, start:props.start, stop:props.stop }
      const url = Routing.generate('simupoll_add_period', {
        sid: this._sid
      })
      //first, display new element, at the correct position
      this._periods.push(result)

      //then, do the background save
      this.$http
        //pass variables to controller
        .post(url, { title: props.title, start:props.start, stop:props.stop })
        .then(
          response => {result.id = response.data},
          //and check if it's alright
          () => {
            this._deletePeriod(result)
            onFail()
            }
        )
    }

    deletePeriod (period, onFail) {
      const url = Routing.generate('simupoll_delete_period', {
        pid: period.id,
        sid: this._sid
      })

      this._deletePeriod(period)

      this.$http
        .delete(url)
        .then(null, () => {
          this._periods.push(period)
          onFail()
        })
    }

    _deletePeriod (period) {
      this._periods.splice(this._periods.indexOf(period), 1)
    }

    editPeriod (originalPeriod, newTitle, newStart, newStop, onFail) {
        //if no change, do nothing
      if (originalPeriod.title === newTitle &&
          originalPeriod.start === newStart &&
          originalPeriod.stop === newStop) {
        return
      }

      //save original value
      const originalTitle = originalPeriod.title
      const originalStart = originalPeriod.start
      const originalStop = originalPeriod.stop
      const url = Routing.generate('simupoll_edit_period', {
        pid: originalPeriod.id
      })

      originalPeriod.title = newTitle
      originalPeriod.start = newStart
      originalPeriod.stop = newStop

      this.$http
        .put(url, { title: newTitle, start: newStart, stop: newStop })
        //if error, rollback
        .then(null, () => {
          originalPeriod.title = originalTitle,
          originalPeriod.start = originalStart,
          originalPeriod.stop = originalStop,
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
