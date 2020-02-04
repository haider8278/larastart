<style>
.widget-user .widget-user-header{
    height: 250px;
}
</style>
<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-3">
                <div class="card card-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header text-white" style="background: url('/images/user-cover.png') center center;">
                        <h3 class="widget-user-username text-left">{{user.name}}</h3>
                        <h5 class="widget-user-desc text-left">{{user.type}}</h5>
                    </div>
                    <div class="widget-user-image">
                        <img id="avatar" class="img-circle" :src="'/images/profile/' +user.photo" alt="User Avatar">
                    </div>
                    <div class="card-footer pt-3">
                        <div class="row">
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                <h5 class="description-header">3,200</h5>
                                <span class="description-text">SALES</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                <h5 class="description-header">13,000</h5>
                                <span class="description-text">FOLLOWERS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4">
                                <div class="description-block">
                                <h5 class="description-header">35</h5>
                                <span class="description-text">PRODUCTS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
            </div>

            <!-- User Settings -->
            <div class="col-md-12 mt-3">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Activity</a></li>
                        <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                        <div class="tab-pane" id="activity">
                            <h2>User Activity</h2>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane active" id="settings">
                            <form class="form-horizontal" @submit.prevent="updateProfile">
                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Name</label>
                                    <div class="col-sm-10">
                                        <input v-model="form.name" type="text" name="name"
                                        placeholder="Name" class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('name') }">
                                        <has-error :form="form" field="name"></has-error>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input v-model="form.email" type="email" name="email" placeholder="Email Address"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                        <has-error :form="form" field="email"></has-error>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="bio" class="col-sm-2 col-form-label">Bio</label>
                                    <div class="col-sm-10">
                                        <textarea v-model="form.bio" name="bio"
                                        placeholder="Bio" class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                                        <has-error :form="form" field="bio"></has-error>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="profile" class="col-sm-2 col-form-label">Profile Photo</label>
                                    <div class="col-sm-10">
                                    <input type="file" class="form-control" id="profile" placeholder="Profile photo" @change="fileReader">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-sm-2 col-form-label">Password (leave empty if not changing)</label>
                                    <div class="col-sm-10">
                                    <input type="password" class="form-control" id="password" placeholder="Password" autocomplete="false">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-success">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            <!-- /User Settings -->
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                user : {},
                form: new Form({
                    id:'',
                    name: '',
                    email: '',
                    password: '',
                    bio: '',
                    photo: '',
                })
            }
        },
        methods:{
            loadPofile(){
                axios.get('api/profile')
                .then(({data}) => {
                    this.form.fill(data);
                    this.user =data;
                })
            },
            updateProfile(){
                this.$Progress.start();
                this.form.put('api/profile')
                .then((result)=>{
                    let data = result.data;
                    this.$Progress.finish();
                    toast.fire({icon: data.type,title: data.title});
                })
                .catch(()=>{
                    this.$Progress.fail();
                });
            },
            fileReader(e){
                let file = e.target.files[0];
                console.log(file);
                if(file['size'] < 2111775){
                    let reader = new FileReader();
                    reader.onloadend = (file)=>{
                        this.form.photo = reader.result;
                        $("#avatar").attr('src', file.target.result);
                    }
                    reader.readAsDataURL(file);
                }else{
                    toast.fire({icon: 'error',title: 'Image size must be less than 2mb'});
                }
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        created(){
            this.loadPofile();
        }
    }
</script>
