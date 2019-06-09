import Vue from 'vue'
import Router from 'vue-router'

import Home from '../pages/Home'
import Register from '../pages/Register'
import User from '../pages/User'
import Login from '../pages/Login'
import Logout from '../pages/Logout'
import Profile from '../pages/Profile'
import Jobs from '../pages/Jobs'
import Notfound from '../pages/Notfound'

Vue.use(Router);


const router = new Router({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'Home',
            component: Home,
        },
        {
            path: '/users',
            name: 'User',
            component: User,
        },

        {
            path: '/useraccount',
            name: 'Profile',
            component: Profile,
        },

        {
            path: '/register',
            name: 'Register',
            component: Register,
        },

        {
            path: '/signin',
            name: 'Login',
            component: Login
        },

        {
            path: '/jobs',
            name: 'Jobs',
            component: Jobs
        },
        

        {
            path: '/logout',
            name: 'Logout',
            component: Logout
        },


        {
            path: '*',
            name: 'Notfound',
            component: Notfound
        },
    ]
});

router.beforeEach((to, from, next) => {


  const isLoggedIn = localStorage.user_id;
  const isAdmin = localStorage.isAdmin == 'true' ? true : false ;

  if (to.fullPath === '/') {
    if(!isLoggedIn) {
        next('/signin');
    }
  }

  if (to.fullPath === '/users') {

    if(!isAdmin) {
        if(isLoggedIn)
            next('/');
        else 
            next('/signin');
    }    
  }


  if (to.fullPath === '/useraccount') {
    if(!isLoggedIn) {
        next('/signin');
    }
  }

  if (to.fullPath === '/register') {
    if(isLoggedIn) {
        next('/useraccount');
    }
  }

  if (to.fullPath === '/signin') {
  }

  if (to.fullPath === '/logout') {
  }

  



  next();
});



export default router;