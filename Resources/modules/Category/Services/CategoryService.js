//no import of Angular stuff ($http, $q…)
export default class CategoryService {
    constructor($http, $q) {
        //declaration of variables
        this.$http = $http
        this.$q = $q
        this._tree = CategoryService._getGlobal('simupollTree')
        this._sid = CategoryService._getGlobal('simupollSid')
        this.parentTree = []
    }

    getTree () {
      return this._tree
    }

    getSid () {
      return this._sid
    }

    /**
     * Retrieve all parent category possible for a given category
     */
    getParentCategoriesFor(category) {
        const url = Routing.generate('simupoll_parent_category', {
          cid: category.id,
          sid: this._sid
        })
        //$http returns a promise
        return this.$http
          .get(url, {cid:category.id, sid:this._sid})
    }

    addCategory (props, category, onFail) {
      let result = { name: category.indent+'-- '+props.name }
      const url = Routing.generate('simupoll_add_category', {
        sid: this._sid
      })
      //first, display new element, at the correct position
      this._tree.splice((this._tree.indexOf(category))+1, 0, result)

      //then, do the background save
      this.$http
        //pass variables to controller
        .post(url, { name: props.name, cid:category.id })
        .then(
          response => {
              result.id = response.data.id
              result.name = response.data.name
           },
          //and if there's an error
          () => {
            //rollback
            this._deleteCategory(result)
            onFail()
            }
        )
    }

    editCategory (originalCategory, newName, newParent, onFail) {
      //if no change, do nothing
      if (originalCategory.name === newName) {
        return
      }

      //save original value
      const originalName = originalCategory.name
      const url = Routing.generate('simupoll_edit_category', {
        id: originalCategory.id
      })

      originalCategory.name = newName.replace(/--/g, "")

      this.$http
        .put(url, { name: newName, parentcategory: newParent })
        //if error, rollback
        .then(null, () => {
          originalCategory.name = originalName
          onFail()
        })
    }

    deleteCategory (category, onFail) {
      const url = Routing.generate('simupoll_delete_category', {
        cid: category.id
      })

      const oldcat = this._deleteCategory(category)

      this.$http
        .delete(url)
        .then(null, () => {
          this._tree.push(oldcat)
          this._tree.push(category)
          onFail()
      })
    }

    /**
    * delete category and sub categories
    */
    _deleteCategory (category) {
        let subcat = {}
        //find subcategories of this category
        const suburl = Routing.generate('simupoll_childof_category', {
          cid: category.id,
          sid: this._sid
        })

        this.$http
          .get(suburl)
          .then(response => {
              subcat = response.data

              //delete children
              for (var i = 0, len = subcat.length; i < len; i++) {
                function findCategory(cat) {
                  return cat.id === subcat[i].id
                }
                let found = this._tree.find(findCategory)
                if (found !== 'undefined') {
                    this._tree.splice(this._tree.indexOf(found), 1)
                }
              }
              //delete category
              this._tree.splice(this._tree.indexOf(category), 1)

              return subcat
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
