//the template for modals
import addPeriodTemplate from '../Partials/modalAddPeriod.html'
import editPeriodTemplate from '../Partials/modalEditPeriod.html'
import deletePeriodTemplate from '../Partials/modalDeletePeriod.html'

export default class PeriodController {
//no import of Angular stuff ($window, $scope…)
    constructor(periodModal) {
        //declaration of variables
        this.periods            = ''
        this.sid                = 0
        this.addedPeriod        = {}
        this.editedPeriod       = {}
        this.errors             = []
        this.errorMessage       = null
        this._modalFactory      = periodModal
        this._modalInstance     = null
    }

    init(periods, sid) {
        this.periods   = JSON.parse(periods)
        this.sid       = sid
    }

    showAddPeriodForm () {
      this._modal(addPeriodTemplate)
    }

    doAddPeriod(form) {
console.log('add')
        if (form.$valid) {
            this._resetForm(form)
            this._closeModal()
        }
    }

    showEditPeriodForm (period, sid) {
    //   this.editedPeriod.original = period
    //   this.editedPeriod.newValue = period.name
      this._modal(editPeriodTemplate)
    }

    doEditPeriod(form) {
console.log('edit')
        if (form.$valid) {
            this._resetForm(form)
            this._closeModal()
        }
    }

    showDeletePeriodForm (period) {
      this._modal(deletePeriodTemplate)
    }

    doDeletePeriod(sid) {
        console.log('deleted');
    }

    //close X modal
    cancel (form) {
console.log('close')
      if (form) {
        this._resetForm(form)
      }
      this._modalInstance.dismiss()
    }

    _modal (template, errorMessage, errors) {
      if (errorMessage) {
        this.errorMessage = errorMessage
      }
      if (errors) {
        this.errors = errors
      }
      this._modalInstance = this._modalFactory.open(template)
    }

    _closeModal () {
        this._modalInstance.close()
    }

    _resetForm (form) {
      this.errorMessage = null
      this.errors = []
      form.$setPristine()
      form.$setUntouched()
    }
}
