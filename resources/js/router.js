import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

// Guards
import { RedirectBasedOnAuthStatus } from "./auth_guards";

// Auth
import Unauthorized from "./components/common/Unauthorized";
import NotFound from "./components/common/NotFound";

// components
import Dashboard from "./components/dashboard/Main";
import Login from "./components/auth/Login";
import PermissionsGroup from "./components/user-management/permissions/PermissionsGroup";
import RolesGroup from "./components/user-management/roles/RolesGroup";
import UsersGroup from "./components/user-management/users/UsersGroup";

import AddProduct from "./components/products/AddProduct";
import Products from "./components/products/Products";

import AddTank from "./components/tanks/AddTank";
import EditTank from "./components/tanks/EditTank";
import Tanks from "./components/tanks/Tanks";

import AddDispenser from "./components/dispensers/AddDispenser";
import EditDispenser from "./components/dispensers/EditDispenser";
import Dispensers from "./components/dispensers/Dispensers";

import AddNozzle from "./components/nozzles/AddNozzle";
import EditNozzle from "./components/nozzles/EditNozzle";
import Nozzles from "./components/nozzles/Nozzles";

import Rates from "./components/rates/Rates";

import EditSetting from "./components/settings/Edit";

import EditUserAccount from "./components/user-management/users/EditUserAccount";

const router = new VueRouter({
    mode: "history",
    routes: [
        {
            path: "*",
            name: "not_found",
            component: NotFound,
            meta: { title: `404 - Not Found - ${process.env.MIX_APP_NAME}` },
        },

        {
            path: "/",
            name: "dashboard",
            component: Dashboard,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Dashboard - ${process.env.MIX_APP_NAME}`,
            },
        },

        {
            path: "/login",
            name: "login",
            component: Login,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Login - ${process.env.MIX_APP_NAME}`,
            },
        },

        {
            path: "/unauthorized",
            name: "unauthorized",
            component: Unauthorized,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `403 - Unauthorized Page - ${process.env.MIX_APP_NAME}`,
            },
        },

        {
            path: "/products/add",
            name: "product_add",
            component: AddProduct,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Product - ${process.env.MIX_APP_NAME}`,
                gate: "product_create",
            },
        },

        {
            path: "/products",
            name: "products",
            component: Products,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Products - ${process.env.MIX_APP_NAME}`,
                gate: "product_access",
            },
        },

        {
            path: "/tanks/add",
            name: "add_tank",
            component: AddTank,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Add Tank - ${process.env.MIX_APP_NAME}`,
                gate: "tank_create",
            },
        },

        {
            path: "/tanks/edit/:id",
            name: "tank_edit",
            component: EditTank,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Tank - ${process.env.MIX_APP_NAME}`,
                gate: "tank_edit",
            },
        },

        {
            path: "/tanks",
            name: "tanks",
            component: Tanks,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Tanks - ${process.env.MIX_APP_NAME}`,
                gate: "tank_access",
            },
        },

        {
            path: "/dispensers/add",
            name: "add_dispenser",
            component: AddDispenser,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Add Dispenser - ${process.env.MIX_APP_NAME}`,
                gate: "dispenser_create",
            },
        },

        {
            path: "/dispensers/edit/:id",
            name: "dispenser_edit",
            component: EditDispenser,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Dispenser - ${process.env.MIX_APP_NAME}`,
                gate: "dispenser_edit",
            },
        },

        {
            path: "/dispensers",
            name: "dispensers",
            component: Dispensers,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Dispensers - ${process.env.MIX_APP_NAME}`,
                gate: "dispenser_access",
            },
        },

        {
            path: "/nozzles/add",
            name: "add_nozzle",
            component: AddNozzle,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Add Nozzle - ${process.env.MIX_APP_NAME}`,
                gate: "nozzle_create",
            },
        },

        {
            path: "/nozzles/edit/:id",
            name: "nozzle_edit",
            component: EditNozzle,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Nozzle - ${process.env.MIX_APP_NAME}`,
                gate: "nozzle_edit",
            },
        },

        {
            path: "/nozzles",
            name: "nozzles",
            component: Nozzles,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Nozzles - ${process.env.MIX_APP_NAME}`,
                gate: "nozzle_access",
            },
        },

        {
            path: "/rates",
            name: "rates",
            component: Rates,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Rates - ${process.env.MIX_APP_NAME}`,
                gate: "rate_access",
            },
        },

        {
            path: "/user-management/permissions",
            name: "permissions",
            component: PermissionsGroup,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Permissions - ${process.env.MIX_APP_NAME}`,
                gate: "permission_access",
            },
        },

        {
            path: "/user-management/roles",
            name: "roles",
            component: RolesGroup,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Roles - ${process.env.MIX_APP_NAME}`,
                gate: "role_access",
            },
        },

        {
            path: "/user-management/users",
            name: "users",
            component: UsersGroup,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Users - ${process.env.MIX_APP_NAME}`,
                gate: "user_access",
            },
        },

        {
            path: "/settings",
            name: "settings",
            component: EditSetting,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit App Setting - ${process.env.MIX_APP_NAME}`,
                gate: "app_setting_edit",
            },
        },

        {
            path: "/edit_user_account",
            name: "edit_user_account",
            component: EditUserAccount,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit User Account - ${process.env.MIX_APP_NAME}`,
            },
        },
    ],
});

router.beforeEach((to, from, next) => {
    document.title = to.meta.title;

    next();
});

export default router;
