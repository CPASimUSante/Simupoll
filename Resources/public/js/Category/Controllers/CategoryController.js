//the template for modals
import addCategoryTemplate from '../Partials/modalAddCategory.html'
import editCategoryTemplate from '../Partials/modalEditCategory.html'
import deleteCategoryTemplate from '../Partials/modalDeleteCategory.html'

export default class CategoryController {
//no import of Angular stuff ($window, $scope…)
    constructor(CategoryService, categoryModal) {
        //declaration of variables
        this.CategoryService    = CategoryService
        this.tree               = ''
        this.sid                = 0
        this.addedCategory      = {}
        this.editedCategory     = {}
        this.errors             = []
        this.errorMessage       = null
        this._modalFactory      = categoryModal
        this._modalInstance     = null
    }

    init(tree, sid) {
        this.tree   = JSON.parse(tree)
        this.sid    = sid
    }

    showAddCategoryForm () {
      this._modal(addCategoryTemplate)
    }

    doAddCategory(form) {
console.log('add')
        if (form.$valid) {
            this._resetForm(form)
            this._closeModal()
        }
    }

    showEditCategoryForm (category, sid) {
    //   this.editedCategory.original = category
    //   this.editedCategory.newValue = category.name
      this._modal(editCategoryTemplate)
    }

    doEditCategory(form) {
console.log('edit')
        if (form.$valid) {
            this._resetForm(form)
            this._closeModal()
        }
    }

    showDeleteCategory(category, sid) {
        this._modal(deleteCategoryTemplate)
    }

    doDeleteCategory(sid) {
console.log('deleted')
        this._closeModal()
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
