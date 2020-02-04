window.routes = [
    { path: '/dashboard', component: require('./components/Dashboard.vue').default },
    { path: '/users', component: require('./components/modules/users/Users.vue').default },
    { path: '/profile', component: require('./components/modules/users/Profile.vue').default },
    { path: '/invoice', component: require('./components/Invoice.vue').default },
    { path: '*', component: require('./components/404.vue').default }
];

