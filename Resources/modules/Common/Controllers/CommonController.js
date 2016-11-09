export default class CommonController {
    //no import of Angular stuff ($window, $scope…)
    constructor(CommonService, modalFactory) {
        //declaration of variables
        this.errors             = []
        this.errorMessage       = null
        this._modalFactory      = modalFactory
        this._modalInstance     = null
        this._service           = CommonService
    }
    
    doDeleteCategory(sid) {
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
