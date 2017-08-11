var UsersList = [];

var UserModel = {
    ID              : null,
    username        : '',
    title           : '',
    firstName       : '',
    middleInitial   : '',
    lastName        : '',
    gender          : '',
    dateOfBirth     : '',
    created_at      : '',
    updated_at      : '',
    hash            : ''
};

var UserModelError = {
    ID              : null,
    username        : null,
    title           : null,
    firstName       : null,
    middleInitial   : null,
    lastName        : null,
    gender          : null,
    dateOfBirth     : null,
    created_at      : null,
    updated_at      : null,
    hash            : null
};

var TitleOptions = {
    'all' : [
        'Mr',
        'Mrs',
        'Miss',
        'Ms',
        'Rev',
        'Dr',
        'Professor'
    ],
    'M' : [
        'Mr',
        'Rev',
        'Dr',
        'Professor'      
    ],
    'F' : [
        'Mrs',
        'Miss',
        'Ms',
        'Dr',
        'Professor'   
    ]
}

var List = Vue.extend({
    template: '#user-list',
    data: function () {
        return {users: UsersList, searchKey: ''};
    },
    mounted : function(){
        this.fetchUsers();
    },
    methods : {
        fetchUsers : function () {
            this.$http.get('/api/user/index').then(function(response){
                var responseObj = response.data;

                if(responseObj.success) this.users = responseObj.response;

            });
        }
    }
});

var UserShow = Vue.extend({
    template: '#user-show',
    mounted : function(){
        this.fetchUser(this.$route.params.user_id);
    },
    data: function () {
        return { user: UserModel , not_found : false } ;
    },
    methods : {
        fetchUser : function (user_id) {
            this.$http.get('/api/user/'+ user_id).then(function(response){
                var responseObj = response.data;
                if(responseObj.status_code == 404){
                    this.not_found = true;
                    return;
                }

                if(responseObj.success) this.user = responseObj.response[0];

            });
        }
    }
});

var UserCreate = Vue.extend({
    template: '#add-user',
    data: function () {
        return { user: UserModel, titles : TitleOptions.all,token : window.codenathan.token,errors : {} }
    },
    methods: {
        createUser: function() {
            var self = this;
            self.user._token = this.token;

            this.$http.post('/api/user/create', self.user).then(function(response){
                var responseObj = response.data;

                if(responseObj.success){
                    self.user = UserModel;
                    router.push('/');
                }else if(responseObj.status_code == "400"){
                    self.errors = responseObj.error;
                }
            });

            return false;



        },
        updateTitle : function () {

            if(this.user.gender in TitleOptions){
                this.titles = TitleOptions[this.user.gender];
            }else{
                this.titles = TitleOptions.all;
            }
        }
    },
    computed :{
        tomorrowDate : function(){
            var date =  moment(new Date()).add(1,'days').format('DD/MM/YYYY');
            return date;
        }
    }
});

var UserEdit = Vue.extend({
    template: '#user-edit',
    data: function () {
        return { user: UserModel, titles : TitleOptions.all,token : window.codenathan.token, errors : {} }
    },
    mounted : function(){
        this.fetchUser(this.$route.params.user_id);
    },
    methods: {
        updateUser: function () {
            var self = this;
            self.user._token = this.token;
            self.user._method = 'patch';

            this.$http.post('/api/user/update', self.user).then(function(response){
                var responseObj = response.data;

                if(responseObj.success){
                    self.user = UserModel;
                    router.push('/');
                }else if(responseObj.status_code == "400"){
                    self.errors = responseObj.error;
                }
            });

            return false;
        },
        fetchUser : function (user_id) {
            this.$http.get('/api/user/'+ user_id).then(function(response){
                var responseObj = response.data;
                if(responseObj.status_code == 404){
                    this.not_found = true;
                    return;
                }

                if(responseObj.success) this.user = responseObj.response[0];

            });
        },
        updateTitle : function () {

            if(this.user.gender in TitleOptions){
                this.titles = TitleOptions[this.user.gender];
            }else{
                this.titles = TitleOptions.all;
            }
        }
    },
    computed :{
        tomorrowDate : function(){
            var date =  moment(new Date()).add(1,'days').format('DD/MM/YYYY');
            return date;
        }
    }
});

var UserDelete = Vue.extend({
    template: '#user-delete',
    data: function () {
        return {user: UserModel,token : window.codenathan.token};
    },
    mounted : function(){

        this.fetchUser(this.$route.params.user_id);
    },
    methods: {
        deleteUser: function () {
            var self = this;
            self.user._token = this.token;
            self.user._method = 'delete'
            this.$http.post('/api/user/delete', self.user).then(function(response){
                var responseObj = response.data;

                if(responseObj.success){
                    router.push('/');
                }else{
                    //check validation
                    //check erros
                }
            });

            return false;
        },
        fetchUser : function (user_id) {
            this.$http.get('/api/user/'+ user_id).then(function(response){
                var responseObj = response.data;
                if(responseObj.status_code == 404){
                    this.not_found = true;
                    return;
                }

                if(responseObj.success) this.user = responseObj.response[0];


            });
        }

    }

});



Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(String(value)).format('DD/MM/YYYY')
    }
});

Vue.filter('formatDateTime',function(value){
   if(value){
       return moment(String(value),"YYYY-MM-D HH:mm:ss").format('MMMM Do YYYY, h:mm:ss a');
   }
});


var router = new VueRouter({routes:[
    { path: '/', component: List},
    { path: '/user/:user_id', component: UserShow, name: 'user-show'},
    { path: '/add-user', component: UserCreate},
    { path: '/user/:user_id/edit', component: UserEdit, name: 'user-edit'},
    { path: '/user/:user_id/delete', component: UserDelete, name: 'user-delete'}
]});

Vue.config.debug = true;
Vue.http.options.emulateJSON = true;

app = new Vue({
    router:router,
    el : '#app',
})