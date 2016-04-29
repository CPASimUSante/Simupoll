//the template for modals
import addPeriodTemplate from '../Partials/modalAddPeriod.html'
import editPeriodTemplate from '../Partials/modalEditPeriod.html'
import deletePeriodTemplate from '../Partials/modalDeletePeriod.html'
import errorTemplate from '../../Common/Partials/modalError.html'

export default class PeriodController {
    //no import of Angular stuff ($window, $scope…)
    constructor(PeriodService, periodModal, dateFormatFilter) {
        this.datepickerOptions  = {langage: 'fr-FR'}
        this.popupStart         = {}
        this.popupStop          = {}
        this.popupStart.opened  = false
        this.popupStop.opened   = false

        //declaration of variables
        this.periods            = PeriodService.getPeriods()
        this.sid                = PeriodService.getSid()
        this.currentPeriod      = {}
        this.addedPeriod        = {}
        this.editedPeriod       = {}
        this._deletedPeriod     = null
        this.errors             = []
        this.errorMessage       = null
        this._modalFactory      = periodModal
        this._modalInstance     = null
        this._service           = PeriodService
        this._datefilter        = dateFormatFilter
    }

    pickerStart() {
        this.popupStart.opened = true
    }
    pickerStop() {
        this.popupStop.opened = true
    }

    showAddPeriod (period) {
        this.currentPeriod = period
        this._modal(addPeriodTemplate)
    }

    doAddPeriod(form) {
        this._service.addPeriod(this.addedPeriod, this.currentPeriod, () => {
            this._modal(errorTemplate, 'period_add_failure')
        })
        if (form.$valid) {
            this._resetForm(form)
            this._closeModal()
        }
    }

    showEditPeriod (period) {
        //save original variables values
//console.log(period.start.date)
        this.editedPeriod.original = period
        this.editedPeriod.title = period.title
        this.editedPeriod.start = this._datefilter(period.start.date)
        this.editedPeriod.stop = this._datefilter(period.stop.date)
        this._modal(editPeriodTemplate)
    }

    doEditPeriod(form) {
        if (form.$valid) {
            this._service.editPeriod(
                this.editedPeriod.original,
                this.editedPeriod.title,
                this.editedPeriod.start,
                this.editedPeriod.stop,
                () => this._modal(errorTemplate, 'period_edit_failure')
            )
            this._resetForm(form)
            this._closeModal()
        }
    }

    showDeletePeriod (period) {
        this._deletedPeriod = period
        this._modal(deletePeriodTemplate)
    }

    doDeletePeriod(sid) {
        this._service.deletePeriod(
          this._deletedPeriod,
          () => this._modal(errorTemplate, 'period_delete_failure')
        )
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
