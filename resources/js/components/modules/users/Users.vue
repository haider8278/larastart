<template>
    <div class="container">
        <not-found v-if="!$gate.isAdmin()"></not-found>
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users Table</h3>

                <div class="card-tools">
                    <button class="btn btn-success" @click="newModal">Add New <i class="fa fa-user-plus"></i></button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th @click="sort('id')">ID <span><i class="fa fa-chevron-down"></i></span></th>
                      <th>photo</th>
                      <th @click="sort('name')">User <span></span></th>
                      <th @click="sort('email')">Email <span></span></th>
                      <th @click="sort('type')">Type <span></span></th>
                      <th>Register At</th>
                      <th>Modify</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="user in users.data" :key="user.id">
                      <td>{{user.id}}</td>
                      <td class="user-panel"><img :src="'/images/profile/' + user.photo" class="img-circle elevation-2"></td>
                      <td>{{user.name | capitalize}}</td>
                      <td>{{user.email}}</td>
                      <td>{{user.type | capitalize}}</td>
                      <td>{{user.created_at | date}}</td>
                      <td>
                          <a href="#" @click="editModal(user)"><i class="fa fa-edit green"></i></a>&nbsp;
                          <a href="#" @click="deleteUser(user.id)"><i class="fa fa-trash red"></i></a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="users" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="addEditModal" tabindex="-1" role="dialog" aria-labelledby="addNewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewModalLabel">{{eidtmode ? 'Edit' : 'Add New'}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="eidtmode ? updateUser() : createUser()">
                    <div class="modal-body">
                        <div class="form-group">
                            <input v-model="form.name" type="text" name="name"
                                placeholder="Name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <input v-model="form.email" type="email" name="email"
                                placeholder="Email Address"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                        </div>
                        <div class="form-group">
                            <select v-model="form.type" name="type"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                <option value="">Select User Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                                <option value="author">Author</option>
                            </select>
                            <has-error :form="form" field="type"></has-error>
                        </div>
                        <div class="form-group">
                            <textarea v-model="form.bio" name="bio"
                                placeholder="Bio"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                            <has-error :form="form" field="bio"></has-error>
                        </div>
                        <div class="form-group">
                            <input v-model="form.password" type="password" name="password"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                            <has-error :form="form" field="password"></has-error>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">{{eidtmode ? 'Update' : 'Create'}}</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                eidtmode : false,
                users : {},
                currentSort:'id',
                currentSortDir:'desc',
                form: new Form({
                    id:'',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    photo: '',
                })
            }
        },
        methods : {

            updateUser(){
                this.$Progress.start();
                this.form.put('api/users/'+this.form.id)
                .then((result)=>{
                    let data = result.data;
                    Fire.$emit('AfterCreate');
                    this.$Progress.finish();
                    toast.fire({icon: data.type,title: data.title});
                })
                .catch(()=>{
                    this.$Progress.fail();
                });
            },
            newModal(){
                this.eidtmode = false;
                this.form.reset();
                $("#addEditModal").modal('show');
            },
            editModal(user){
                this.eidtmode = true;
                this.form.clear();
                this.form.reset();
                $("#addEditModal").modal('show');
                this.form.fill(user)
            },
            getResults(page = 1) {
                axios.get('api/users?page=' + page)
                    .then(response => {
                        this.users = response.data;
                    });
		    },
            loadUsers(){
                if(this.$gate.isAdmin()){
                    this.$Progress.start();
                    axios.get('api/users')
                    .then(({data}) => {this.users = data;this.$Progress.finish()})
                    .catch(function (error) {
                        this.$Progress.fail()
                        console.log(error.response.data);
                        console.log(error.response.data.message);
                        console.log(error.response.status);
                        console.log(error.response.headers);
                    });
                }
            },
            createUser(){
                this.$Progress.start();
                this.form.post('api/users')
                .then(()=>{

                    Fire.$emit('AfterCreate');
                    this.$Progress.finish();
                    toast.fire({icon: 'success',title: 'User created successfully'});
                })
                .catch(()=>{
                    this.$Progress.fail();
                });
            },
            deleteUser(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                    })
                .then((result) => {
                    if (result.value) {
                        this.form.delete('api/users/'+id)
                        .then((result)=>{
                            let data = result.data;
                            Fire.$emit('AfterCreate');
                            Swal.fire(
                                data.title,
                                data.msg,
                                data.type
                                );

                        })
                        .catch(function (error){
                            console.log(error);
                            Swal.fire(
                                    'Error '+error.response.status,
                                    error.response.data.message,
                                    'error'
                                );
                        });
                    }
                });

            },
            sort(s) {
                //if s == current sort, reverse
                if(s === this.currentSort) {
                    this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
                }
                $("table th span").html("");
                if(this.currentSortDir == 'asc'){
                    $(event.target).find('span').html('<i class="fa fa-chevron-up"></i>');
                }else{
                    $(event.target).find('span').html('<i class="fa fa-chevron-down"></i>');
                }
                this.currentSort = s;
                axios.get('api/users?orderby='+s+'&order='+this.currentSortDir)
                    .then(({data}) => {this.users = data;this.$Progress.finish()})
                    .catch(function (error) {
                        this.$Progress.fail()
                    });
            }
        },
        created() {
            Fire.$on('searching', (s)=>{
                console.log(s);
                axios.get('api/users?s='+s)
                    .then(({data}) => {this.users = data;this.$Progress.finish()})
                    .catch(function (error) {
                        this.$Progress.fail()
                    });

            })
            this.loadUsers();
            Fire.$on('AfterCreate',()=>{
                $("#addEditModal").modal('hide');
                this.loadUsers();
            });
        },
        mounted () {
        },
    }
</script>
