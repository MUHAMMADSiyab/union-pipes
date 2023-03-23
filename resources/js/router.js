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

import BanksGroup from "./components/banks/BanksGroup";

import ExpenseSourcesGroup from "./components/expense_sources/ExpenseSourcesGroup";

import AddExpense from "./components/expenses/AddExpense";
import EditExpense from "./components/expenses/EditExpense";
import Expenses from "./components/expenses/Expenses";

import AddEmployee from "./components/employees/AddEmployee";
import Employees from "./components/employees/Employees";

import SalariesView from "./components/salaries/View.vue";

import AddCompany from "./components/companies/AddCompany";
import EditCompany from "./components/companies/EditCompany";
import Companies from "./components/companies/Companies";

import AddCustomer from "./components/customers/AddCustomer";
import EditCustomer from "./components/customers/EditCustomer";
import Customers from "./components/customers/Customers";

import PurchaseItemsGroup from "./components/purchase_items/PurchaseItemsGroup";

import AddPurchase from "./components/purchases/AddPurchase";
import EditPurchase from "./components/purchases/EditPurchase";
import Purchases from "./components/purchases/Purchases";
import PurchaseDetails from "./components/purchases/PurchaseDetails";

import PaymentReceipt from "./components/globals/payments/PaymentReceipt";

import PurchaseReportSearch from "./components/reports/purchase/PurchaseReportSearch";
import PurchasedItemsReportSearch from "./components/reports/purchased_items/PurchasedItemsReportSearch";

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
            path: "/expense_sources",
            name: "expense_sources",
            component: ExpenseSourcesGroup,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Expense Sources - ${process.env.MIX_APP_NAME}`,
                gate: "expense_access",
            },
        },

        {
            path: "/expenses/add",
            name: "expense_add",
            component: AddExpense,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Expense - ${process.env.MIX_APP_NAME}`,
                gate: "expense_create",
            },
        },

        {
            path: "/expenses/edit/:id",
            name: "expense_edit",
            component: EditExpense,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Expense - ${process.env.MIX_APP_NAME}`,
                gate: "expense_edit",
            },
        },

        {
            path: "/expenses",
            name: "expenses",
            component: Expenses,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Expenses - ${process.env.MIX_APP_NAME}`,
                gate: "expense_access",
            },
        },

        {
            path: "/employees/add",
            name: "employee_add",
            component: AddEmployee,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Employee - ${process.env.MIX_APP_NAME}`,
                gate: "employee_create",
            },
        },

        {
            path: "/employees",
            name: "employees",
            component: Employees,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Employees - ${process.env.MIX_APP_NAME}`,
                gate: "employee_access",
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
            path: "/salaries/:employee_id",
            name: "salaries_view",
            component: SalariesView,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Employee's Salaries - ${process.env.MIX_APP_NAME}`,
                gate: "salary_access",
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
            path: "/purchase_items",
            name: "purchase_items",
            component: PurchaseItemsGroup,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Purchase Items - ${process.env.MIX_APP_NAME}`,
                gate: "purchase_item_access",
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
            path: "/payments/:id/receipt",
            name: "payment_receipt",
            component: PaymentReceipt,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Payment Receipt - ${process.env.MIX_APP_NAME}`,
                gate: "payment_access",
            },
        },

        {
            path: "/reports/purchase",
            name: "purchase_report",
            component: PurchaseReportSearch,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Purchase Report - ${process.env.MIX_APP_NAME}`,
                gate: "report_access",
            },
        },

        {
            path: "/reports/purchased_items",
            name: "purchased_items_report",
            component: PurchasedItemsReportSearch,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Purchased Items Report - ${process.env.MIX_APP_NAME}`,
                gate: "report_access",
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
