<template>
    <div>
<div class="row page-titles" >
    <div class="col-md-12 align-self-center">
            <div class="d-flex justify-content-end align-items-center">
                <button type="button" class="btn btn-info d-lg-block m-l-15" data-toggle="modal" data-target="#add-categorie" @click="cancel"><i class="fa fa-plus-circle"></i> New Category</button>
                <input type="hidden" class="btn btn-info d-lg-block m-l-15" data-toggle="modal" data-target="#add-categorie" ref="showModal" >
                <div id="add-categorie" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" ref="closeModal">×</button>
                            </div>
                            <form class="form-horizontal" method="post" @submit.prevent="onCreate">
                                <div class="modal-body">
                                    <div :class="{'form-group': true, 'has-danger': errors.has('name') }">
                                        <div class="col-md-12 m-b-20">
                                        <input name="name" v-model="form.name" v-validate="'required'" :class="{'form-control': true, 'form-control-danger': errors.has('name') }" type="text" placeholder="Name">
                                        <small v-show="errors.has('name')" class="form-control-feedback">{{ errors.first('name') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer" v-if="edit">
                                    <input type="button" class="btn btn-info" @click="onUpdate" value="Update" >
                                    <button type="button" class="btn btn-warning waves-effect" data-dismiss="modal">Cancel</button>
                                </div>
                                <div class="modal-footer" v-else>
                                    <input type="submit" class="btn btn-info" value="Add">
                                </div>
                            </form>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row" >
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Categories list</h4>
                    <h6 class="card-subtitle"></h6>
                    <div class="table-responsive m-t-40">
                        <table id="example23" class="display nowrap table table-hover table-striped" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Name</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>N°</th>
                                    <th>Name</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                <tr  v-for="(category, index) in categories">
                                    <td>{{index+1}}</td>
                                    <td>{{category.name}}</td>
                                    <td>{{category.created_at}}</td>
                                    <td>
                                    <button type="submit" @click="onEdit(category)" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn"><i class="ti-pencil-alt" aria-hidden="true"></i></button>
                                    <button type="submit" @click="onDelete(category)" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn"><i class="ti-close" aria-hidden="true"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>

export default {
    props:['response'],
    data () {
        return {
            edit:false,
            categories:[],
            form:{
                name:'',
            }
        }
    },
    created(){
        this.categories = this.response;
    },
    mounted(){ 
        var table = $('#example23').DataTable({
            dom: 'Bfrtip',
            buttons: [
            {
                extend: 'copy',
                exportOptions: {
                    columns: [ 0, 1, 2]
                }
            },

            {
                extend: 'excel',
                exportOptions: {
                    columns: [ 0, 1, 2]
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: [ 0, 1, 2]
                },
            },
            ],
        });
    },
    methods:{
        cancel(){
            this.form = {
                name:''
            }
            this.$validator.reset();
            this.edit = false;
        },
        onCreate() {
            this.$validator.validateAll().then(result => {
                if (!this.errors.any()) {
                    this.loading = true
                    axios.post(route('category.add'), this.form).then(res => {
    
                        this.loading = false
                        let type = 'error'
                        let title = 'Error!'
                        let text = (res.data.msg !== '') ? res.data.msg : 'Please retry later'

                        if(res.data.result) {
                            this.categories.unshift(res.data.category);
                            //this.categories = res.data.categories
                            type="success"
                            title = 'Added!'
                            text = "Your category has been added."
                            this.form = {
                                name:''
                            }      
                            this.$refs.closeModal.click()
                            this.$validator.reset();
                        }

                        this.$swal.fire({
                            type: type,
                            title: title,
                            text: text,
                            showConfirmButton: false,
                            showCloseButton: true,
                            
                        })
                        
                        
                        //this.form = res.data.result;

                    }).catch(e => {
                        console.log('createForm', e)
                        this.loading = false
                    })

                }
            })
        },
        onEdit(category) {
            this.edit = true;
            this.form = category;
            this.$refs.showModal.click()
        },
        onUpdate() {
            this.$validator.validateAll().then(result => {
                if (!this.errors.any()) {
                    this.loading = true
                    axios.put(route('category.update'), this.form).then(res => {

                        this.loading = false
                        let type = 'error'
                        let title = 'Error!'
                        let text = (res.data.msg !== '') ? res.data.msg : 'Please retry later'

                        if(res.data.result) {
                            type="success"
                            title = 'Updated!'
                            text = 'Your category has been updated.'
                            this.form = {
                                name:''
                            }      
                            this.$refs.closeModal.click()
                            this.$validator.reset();
                        }

                        this.$swal.fire({
                            type: type,
                            title: title,
                            text: text,
                            showConfirmButton: false,
                            showCloseButton: true,
                        })
                        
                        
                        //this.form = res.data.result;

                    }).catch(e => {
                        console.log('createForm', e)
                        this.loading = false
                    })
                }
            })
        },
        onDelete(category) {
            this.$swal.fire({
                title: 'Are you sure?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.value) {
                    axios.delete(route('category.delete', category.id)).then(res => {

                        this.loading = false
                        let type = 'error'
                        let title = 'Error!'
                        let text = (res.data.msg !== '') ? res.data.msg : 'Please retry later'

                        if(res.data.result) {
                            var position = this.categories.indexOf(category);
                            this.categories.splice(position, 1);

                            type = "success"
                            title = 'Deleted!'
                            text = "Your category has been deleted."
                            this.edit = false;
                        }

                        this.$swal.fire({
                            type: type,
                            title: title,
                            text: text,
                            showConfirmButton: false,
                            showCloseButton: true,
                        })
                    
                    
                    //this.form = res.data.result;

                    }).catch(e => {
                        console.log('createForm', e)
                        this.loading = false
                    })  
                }
            })
        },
    },
    
       
}
</script>