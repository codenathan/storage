var users = [];
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
    return users[findUserID(user_id)];
};

function findUserID (user_id) {
    for (var key = 0; key < users.length; key++) {
        if (users[key].id == user_id) {
            return key;
        }
    }
};

var List = Vue.extend({
    template: '#user-list',
    data: function () {
        return {users: users, searchKey: ''};
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

var User = Vue.extend({
    template: '#user',
    data: function () {
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
            users[findUserID(user.id)] = {
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
            users.splice(findUserID(this.$route.params.user_id), 1);
            router.push('/');
        }
    }
});

var AddUser = Vue.extend({
    template: '#add-user',
    data: function () {
        return {user: UserModel }
    },
    methods: {
        creatUser: function() {


            var user = this.user;

            users.push({
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
    { path: '/user/:user_id', component: User, name: 'user'},
    { path: '/add-user', component: AddUser},
    { path: '/user/:user_id/edit', component: UserEdit, name: 'user-edit'},
    { path: '/user/:user_id/delete', component: UserDelete, name: 'user-delete'}
]});

app = new Vue({
    router:router,
    el : '#app'
})