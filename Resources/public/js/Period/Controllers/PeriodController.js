//the template for modals
import addPeriodTemplate from '../Partials/modalAddPeriod.html'
import editPeriodTemplate from '../Partials/modalEditPeriod.html'
import deletePeriodTemplate from '../Partials/modalDeletePeriod.html'

export default class PeriodController {
//no import of Angular stuff ($window, $scope…)
//    constructor($window, $scope, periodModal, PeriodService) {
    constructor(PeriodService) {
        //declaration of variables
        this.periods            = ''
        this.sid                = 0
        this.addedPeriod        = {}
        this.editedPeriod       = {}
        //this._modalFactory      = periodModal
        this._modalInstance     = null
    }

    init(periods, sid) {
        this.periods   = JSON.parse(periods)
        this.sid       = sid
    }

    showAddPeriodForm (period) {
      this._modal(addPeriodTemplate)
    }

    showEditPeriodForm (period) {
    //   this.editedPeriod.original = period
    //   this.editedPeriod.newValue = period.name
      this._modal(editPeriodTemplate)
    }

    showDeletePeriodForm (period) {
    //   this.editedPeriod.original = period
    //   this.editedPeriod.newValue = period.name
      this._modal(deletePeriodTemplate)
    }
    
    editPeriod(sid) {
        console.log('edited');
    }

    addPeriod(sid) {
        console.log('add');
    }

    deletePeriod(sid) {
        console.log('deleted');
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
}
