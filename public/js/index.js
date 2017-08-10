var UsersList = [];
var UserModel = {

    username        : '',
    title           : '',
    firstName       : '',
    middleInitial   : '',
    lastName        : '',
    gender          : '',
    dateOfBirth     : ''
};

function findUser (user_id) {
    return UsersList[findUserID(user_id)];
};

function findUserID (user_id) {
    for (var key = 0; key < UsersList.length; key++) {
        if (UsersList[key].id == user_id) {
            return key;
        }
    }
};

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
    data: function () {
        console.log(this.$route.params);
        return {user: findUser(this.$route.params.user_id)};
    }
});

var UserEdit = Vue.extend({
    template: '#user-edit',
    data: function () {
        return {user: findUser(this.$route.params.userid)};
    },
    methods: {
        updateUser: function () {
            var user = this.user;
            UsersList[findUserID(user.id)] = {
                id              : user.id,
                username        : user.username,
                firstName       : user.firstName,
                middleInitial   : user.middleInitial,
                lastName        : user.lastName,
                gender          : user.gender,
                dateOfBirth     : user.dateOfBirth
            };
            router.push('/');
        }
    }
});

var UserDelete = Vue.extend({
    template: '#user-delete',
    data: function () {
        return {user: findUser(this.$route.params.user_id)};
    },
    methods: {
        deleteUser: function () {
            UsersList.splice(findUserID(this.$route.params.user_id), 1);
            router.push('/');
        }
    }
});

var UserCreate = Vue.extend({
    template: '#add-user',
    data: function () {
        return {user: UserModel }
    },
    methods: {
        creatUser: function() {


            var user = this.user;

            UsersList.push({
                id              : user.id,
                username        : user.username,
                firstName       : user.firstName,
                middleInitial   : user.middleInitial,
                lastName        : user.lastName,
                gender          : user.gender,
                dateOfBirth     : user.dateOfBirth
            });
            router.push('/');
        }
    }
});

Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(String(value)).format('DD/MM/YYYY')
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

app = new Vue({
    router:router,
    el : '#app'
})