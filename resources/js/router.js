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

import AddMeter from "./components/meters/AddMeter";
import EditMeter from "./components/meters/EditMeter";
import Meters from "./components/meters/Meters";

import Rates from "./components/rates/Rates";

import BanksGroup from "./components/banks/BanksGroup";

import AddVehicle from "./components/vehicles/AddVehicle";
import EditVehicle from "./components/vehicles/EditVehicle";
import Vehicles from "./components/vehicles/Vehicles";

import AddTransaction from "./components/transactions/AddTransaction";
import EditTransaction from "./components/transactions/EditTransaction";
import Transactions from "./components/transactions/Transactions";

import AddUtility from "./components/utilities/AddUtility";
import EditUtility from "./components/utilities/EditUtility";
import Utilities from "./components/utilities/Utilities";

import AddCompany from "./components/companies/AddCompany";
import EditCompany from "./components/companies/EditCompany";
import Companies from "./components/companies/Companies";

import AddPurchase from "./components/purchases/AddPurchase";
import EditPurchase from "./components/purchases/EditPurchase";
import Purchases from "./components/purchases/Purchases";
import PurchaseDetails from "./components/purchases/PurchaseDetails";

import AddSell from "./components/sells/AddSell";
import EditSell from "./components/sells/EditSell";
import Sells from "./components/sells/Sells";
import SellDetails from "./components/sells/SellDetails";

import AddCustomer from "./components/customers/AddCustomer";
import EditCustomer from "./components/customers/EditCustomer";
import Customers from "./components/customers/Customers";

import AddAccount from "./components/accounts/AddAccount";
import EditAccount from "./components/accounts/EditAccount";
import Accounts from "./components/accounts/Accounts";

import EditSetting from "./components/settings/Edit";

import EditPaymentSetting from "./components/payment_settings/EditPaymentSetting";

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
            path: "/meters/add",
            name: "add_meter",
            component: AddMeter,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Add Meter - ${process.env.MIX_APP_NAME}`,
                gate: "meter_create",
            },
        },

        {
            path: "/meters/edit/:id",
            name: "meter_edit",
            component: EditMeter,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Meter - ${process.env.MIX_APP_NAME}`,
                gate: "meter_edit",
            },
        },

        {
            path: "/meters",
            name: "meters",
            component: Meters,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Meters - ${process.env.MIX_APP_NAME}`,
                gate: "meter_access",
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
            path: "/banks",
            name: "banks",
            component: BanksGroup,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Banks - ${process.env.MIX_APP_NAME}`,
                gate: "bank_access",
            },
        },

        {
            path: "/vehicles/add",
            name: "add_vehicle",
            component: AddVehicle,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Add Vehicle - ${process.env.MIX_APP_NAME}`,
                gate: "tank_create",
            },
        },

        {
            path: "/vehicles/edit/:id",
            name: "vehicle_edit",
            component: EditVehicle,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Vehicle - ${process.env.MIX_APP_NAME}`,
                gate: "vehicle_edit",
            },
        },

        {
            path: "/vehicles",
            name: "vehicles",
            component: Vehicles,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Vehicles - ${process.env.MIX_APP_NAME}`,
                gate: "vehicle_access",
            },
        },

        {
            path: "/transactions/add",
            name: "transaction_add",
            component: AddTransaction,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Transaction - ${process.env.MIX_APP_NAME}`,
                gate: "transaction_create",
            },
        },

        {
            path: "/transactions/edit/:id",
            name: "transaction_edit",
            component: EditTransaction,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Transaction - ${process.env.MIX_APP_NAME}`,
                gate: "transaction_edit",
            },
        },

        {
            path: "/transactions",
            name: "transactions",
            component: Transactions,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Transactions - ${process.env.MIX_APP_NAME}`,
                gate: "transaction_access",
            },
        },

        {
            path: "/utilities/add",
            name: "utility_add",
            component: AddUtility,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Utility - ${process.env.MIX_APP_NAME}`,
                gate: "utility_create",
            },
        },

        {
            path: "/utilities/edit/:id",
            name: "utility_edit",
            component: EditUtility,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Utility - ${process.env.MIX_APP_NAME}`,
                gate: "utility_edit",
            },
        },

        {
            path: "/utilities",
            name: "utilities",
            component: Utilities,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Utilities - ${process.env.MIX_APP_NAME}`,
                gate: "utility_access",
            },
        },

        {
            path: "/companies/add",
            name: "company_add",
            component: AddCompany,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Company - ${process.env.MIX_APP_NAME}`,
                gate: "company_create",
            },
        },

        {
            path: "/companies",
            name: "companies",
            component: Companies,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Companies - ${process.env.MIX_APP_NAME}`,
                gate: "company_access",
            },
        },

        {
            path: "/companies/edit/:id",
            name: "company_edit",
            component: EditCompany,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Company - ${process.env.MIX_APP_NAME}`,
                gate: "company_edit",
            },
        },

        {
            path: "/purchases/add",
            name: "purchase_add",
            component: AddPurchase,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Purchase - ${process.env.MIX_APP_NAME}`,
                gate: "purchase_create",
            },
        },

        {
            path: "/purchases/edit/:id",
            name: "purchase_edit",
            component: EditPurchase,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Purchase - ${process.env.MIX_APP_NAME}`,
                gate: "purchase_edit",
            },
        },

        {
            path: "/purchases",
            name: "purchases",
            component: Purchases,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Purchases - ${process.env.MIX_APP_NAME}`,
                gate: "purchase_access",
            },
        },

        {
            path: "/purchases/:id",
            name: "purchase_details",
            component: PurchaseDetails,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Purchase Details - ${process.env.MIX_APP_NAME}`,
                gate: "purchase_show",
            },
        },

        {
            path: "/sells/add",
            name: "sell_add",
            component: AddSell,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Sell - ${process.env.MIX_APP_NAME}`,
                gate: "sell_create",
            },
        },

        {
            path: "/sells/edit/:id",
            name: "sell_edit",
            component: EditSell,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Sell - ${process.env.MIX_APP_NAME}`,
                gate: "sell_edit",
            },
        },

        {
            path: "/sells",
            name: "sells",
            component: Sells,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Sells - ${process.env.MIX_APP_NAME}`,
                gate: "sell_access",
            },
        },

        {
            path: "/sells/:id",
            name: "sell_details",
            component: SellDetails,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Sell Details - ${process.env.MIX_APP_NAME}`,
                gate: "sell_show",
            },
        },

        {
            path: "/customers/add",
            name: "customer_add",
            component: AddCustomer,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Customer - ${process.env.MIX_APP_NAME}`,
                gate: "customer_create",
            },
        },

        {
            path: "/customers",
            name: "customers",
            component: Customers,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Customers - ${process.env.MIX_APP_NAME}`,
                gate: "customer_access",
            },
        },

        {
            path: "/customers/edit/:id",
            name: "customer_edit",
            component: EditCustomer,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Customer - ${process.env.MIX_APP_NAME}`,
                gate: "customer_edit",
            },
        },

        {
            path: "/accounts/add",
            name: "account_add",
            component: AddAccount,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Account - ${process.env.MIX_APP_NAME}`,
                gate: "account_create",
            },
        },

        {
            path: "/accounts/edit/:id",
            name: "account_edit",
            component: EditAccount,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Account - ${process.env.MIX_APP_NAME}`,
                gate: "account_edit",
            },
        },

        {
            path: "/accounts/customer/:id",
            name: "account_access",
            component: Accounts,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Account Entries - ${process.env.MIX_APP_NAME}`,
                gate: "account_access",
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
            path: "/payment_settings",
            name: "payment_settings",
            component: EditPaymentSetting,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Payment Setting - ${process.env.MIX_APP_NAME}`,
                gate: "payment_setting_edit",
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
