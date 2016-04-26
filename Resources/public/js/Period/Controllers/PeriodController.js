//the template for modals
import addPeriodTemplate from '../Partials/modalAddPeriod.html'
import editPeriodTemplate from '../Partials/modalEditPeriod.html'
import deletePeriodTemplate from '../Partials/modalDeletePeriod.html'
import errorTemplate from '../../Common/Partials/modalError.html'

export default class PeriodController {
    //no import of Angular stuff ($window, $scope…)
    constructor(PeriodService, periodModal) {
        //declaration of variables
        this.periods            = PeriodService.getPeriods()
        this.sid                = PeriodService.getSid()
        this.addedPeriod        = {}
        this.editedPeriod       = {}
        this.errors             = []
        this.errorMessage       = null
        this._modalFactory      = periodModal
        this._modalInstance     = null
        this._service           = PeriodService
    }

    showAddPeriodForm () {
      this._modal(addPeriodTemplate)
    }

    doAddPeriod(form) {
        this._service.addCategory(this.addedCategory, () => {
            this._modal(errorTemplate, 'errors.mark.creation_failure')
        })
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
        this._closeModal()
    }

    //close X modal
    cancel (form) {
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
