//the template for modals
import addCategoryTemplate from '../Partials/modalAddCategory.html'
import editCategoryTemplate from '../Partials/modalEditCategory.html'
import deleteCategoryTemplate from '../Partials/modalDeleteCategory.html'
import errorTemplate from '../../Common/Partials/modalError.html'

export default class CategoryController {
    //no import of Angular stuff ($window, $scope…)
    constructor(CategoryService, categoryModal) {
        //declaration of variables
        this.tree               = CategoryService.getTree()
        this.parentTree         = {}
        this.sid                = CategoryService.getSid()
        this.currentCategory    = {}
        this.currentParent      = {}
        this.addedCategory      = {}
        this.editedCategory     = {}
        //variable containing the category to be deleted
        this._deletedCategory   = null
        this.errors             = []
        this.errorMessage       = null
        this._modalFactory      = categoryModal
        this._modalInstance     = null
        this._service           = CategoryService
    }

    showAddCategoryForm(category) {
        this.currentCategory = category
        this._modal(addCategoryTemplate)
    }

    doAddCategory(form) {
        this._service.addCategory(this.addedCategory, this.currentCategory, () => {
            this._modal(errorTemplate, 'category_add_failure')
        })
        if (form.$valid) {
            this._resetForm(form)
            this._closeModal()
        }
    }

    showEditCategoryForm(category, sid) {
        //save original variables values
        this.editedCategory.original = category
        this.editedCategory.newName = category.name
        //get possible parent categories for this category
        const mod = this._modal(editCategoryTemplate)
        this.parentTree = this._service.getParentCategoriesFor(category, mod)
        //this._modal(editCategoryTemplate)
    }

    doEditCategory(form) {
        if (form.$valid) {
            this._service.editCategory(
                this.editedCategory.original,
                this.editedCategory.newName,
                () => this._modal(errorTemplate, 'category_edition_failure')
            )
            this._resetForm(form)
            this._closeModal()
        }
    }

    showDeleteCategory(category) {
        this._deletedCategory = category
        this._modal(deleteCategoryTemplate)
    }

    doDeleteCategory() {
        this._service.deleteCategory(
          this._deletedCategory,
          () => this._modal(errorTemplate, 'category_delete_failure')
        )
        this._closeModal()
    }

    //close X modal
    cancel(form) {
      if (form) {
        this._resetForm(form)
      }
      this._modalInstance.dismiss()
    }

    _modal(template, errorMessage, errors) {
        if (errorMessage) {
            this.errorMessage = errorMessage
        }
        if (errors) {
            this.errors = errors
        }
        this._modalInstance = this._modalFactory.open(template)
    }

    _closeModal() {
        this._modalInstance.close()
    }

    _resetForm(form) {
      this.errorMessage = null
      this.errors = []
      form.$setPristine()
      form.$setUntouched()
    }
}
