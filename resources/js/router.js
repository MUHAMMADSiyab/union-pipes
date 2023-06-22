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

import AddBank from "./components/banks/AddBank";
import Banks from "./components/banks/Banks";
import BankLedgerEntries from "./components/banks/LedgerEntries";

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
import CompanyLedgerEntries from "./components/companies/LedgerEntries";

import AddCustomer from "./components/customers/AddCustomer";
import EditCustomer from "./components/customers/EditCustomer";
import Customers from "./components/customers/Customers";
import CustomerLedgerEntries from "./components/customers/LedgerEntries";

import PurchaseItemsGroup from "./components/purchase_items/PurchaseItemsGroup";

import AddPurchase from "./components/purchases/AddPurchase";
import EditPurchase from "./components/purchases/EditPurchase";
import Purchases from "./components/purchases/Purchases";
import PurchaseDetails from "./components/purchases/PurchaseDetails";

import AddSell from "./components/sells/AddSell";
import EditSell from "./components/sells/EditSell";
import Sells from "./components/sells/Sells";
import SellDetails from "./components/sells/SellDetails";

import AddTransaction from "./components/transactions/AddTransaction";
import EditTransaction from "./components/transactions/EditTransaction";
import Transactions from "./components/transactions/Transactions";

import PaymentReceipt from "./components/globals/payments/PaymentReceipt";

import PurchaseReportSearch from "./components/reports/purchase/PurchaseReportSearch";
import PurchasedItemsReportSearch from "./components/reports/purchased_items/PurchasedItemsReportSearch";
import SellReportSearch from "./components/reports/sell/SellReportSearch";
import SoldItemsReportSearch from "./components/reports/sold_items/SoldItemsReportSearch";
import ReceivablesReport from "./components/reports/sell/ReceivablesReport";
import PayablesReport from "./components/reports/purchase/PayablesReport";
import ExpenseReportSearch from "./components/reports/expense/ExpenseReportSearch";
import MachineReportSearch from "./components/reports/machine/MachineReportSearch";
import SalaryReport from "./components/reports/salary/SalaryReport";
import ClosingReportSearch from "./components/reports/closing/ClosingReportSearch";

import CreateGatePass from "./components/gate_passes/CreateGatePass";
import GatePasses from "./components/gate_passes/GatePasses";
import GatePass from "./components/gate_passes/GatePass";

import AddStockItem from "./components/stock_items/AddStockItem";
import EditStockItem from "./components/stock_items/EditStockItem";
import StockItems from "./components/stock_items/StockItems";

import AddMachine from "./components/machines/AddMachine";
import Machines from "./components/machines/Machines";

import AddProduction from "./components/productions/AddProduction";
import EditProduction from "./components/productions/EditProduction";
import Productions from "./components/productions/Productions";

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
            component: Banks,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Banks - ${process.env.MIX_APP_NAME}`,
                gate: "bank_access",
            },
        },

        {
            path: "/banks/:id/ledger_entries",
            name: "bank_ledger_entries",
            component: BankLedgerEntries,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Ledger Entries - ${process.env.MIX_APP_NAME}`,
                gate: "bank_access",
            },
        },

        {
            path: "/banks/add",
            name: "bank_add",
            component: AddBank,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Add Bank - ${process.env.MIX_APP_NAME}`,
                gate: "bank_create",
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
            path: "/companies/:id/ledger_entries",
            name: "company_ledger_entries",
            component: CompanyLedgerEntries,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Ledger Entries - ${process.env.MIX_APP_NAME}`,
                gate: "company_access",
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
            path: "/customers/:id/ledger_entries",
            name: "customer_ledger_entries",
            component: CustomerLedgerEntries,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Ledger Entries - ${process.env.MIX_APP_NAME}`,
                gate: "customer_access",
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
            path: "/reports/sell",
            name: "sell_report",
            component: SellReportSearch,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Sell Report - ${process.env.MIX_APP_NAME}`,
                gate: "report_access",
            },
        },

        {
            path: "/reports/sold_items",
            name: "sold_items_report",
            component: SoldItemsReportSearch,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Sold Items Report - ${process.env.MIX_APP_NAME}`,
                gate: "report_access",
            },
        },

        {
            path: "/reports/receivables",
            name: "receivables_report",
            component: ReceivablesReport,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Receivables Report - ${process.env.MIX_APP_NAME}`,
                gate: "report_access",
            },
        },

        {
            path: "/reports/payables",
            name: "payables_report",
            component: PayablesReport,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Payables Report - ${process.env.MIX_APP_NAME}`,
                gate: "report_access",
            },
        },

        {
            path: "/reports/expense",
            name: "expense_report",
            component: ExpenseReportSearch,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Expense Report - ${process.env.MIX_APP_NAME}`,
                gate: "report_access",
            },
        },

        {
            path: "/reports/machine",
            name: "machine_report",
            component: MachineReportSearch,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Machine Report - ${process.env.MIX_APP_NAME}`,
                gate: "report_access",
            },
        },

        {
            path: "/reports/salary",
            name: "salary_report",
            component: SalaryReport,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Salary Report - ${process.env.MIX_APP_NAME}`,
                gate: "report_access",
            },
        },

        {
            path: "/reports/closing",
            name: "closing_report",
            component: ClosingReportSearch,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Closing Report - ${process.env.MIX_APP_NAME}`,
                gate: "report_access",
            },
        },

        {
            path: "/gate_passes/add",
            name: "gate_pass_add",
            component: CreateGatePass,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Gate Pass - ${process.env.MIX_APP_NAME}`,
                gate: "gate_pass_create",
            },
        },

        {
            path: "/gate_passes",
            name: "gate_pass_access",
            component: GatePasses,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Gate Passes - ${process.env.MIX_APP_NAME}`,
                gate: "gate_pass_access",
            },
        },

        {
            path: "/gate_passes/:id",
            name: "gate_pass_show",
            component: GatePass,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Gate Pass - ${process.env.MIX_APP_NAME}`,
                gate: "gate_pass_access",
            },
        },

        {
            path: "/stock_items/add",
            name: "stock_item_add",
            component: AddStockItem,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Stock Item - ${process.env.MIX_APP_NAME}`,
                gate: "stock_item_create",
            },
        },

        {
            path: "/stock_items",
            name: "stock_items",
            component: StockItems,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Stock Items - ${process.env.MIX_APP_NAME}`,
                gate: "stock_item_access",
            },
        },

        {
            path: "/stock_items/edit/:id",
            name: "stock_item_edit",
            component: EditStockItem,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Stock Item - ${process.env.MIX_APP_NAME}`,
                gate: "stock_item_edit",
            },
        },

        {
            path: "/machines/add",
            name: "machine_add",
            component: AddMachine,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Machine - ${process.env.MIX_APP_NAME}`,
                gate: "machine_create",
            },
        },

        {
            path: "/machines",
            name: "machines",
            component: Machines,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Machines - ${process.env.MIX_APP_NAME}`,
                gate: "machine_access",
            },
        },

        {
            path: "/productions/add",
            name: "production_add",
            component: AddProduction,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `New Production - ${process.env.MIX_APP_NAME}`,
                gate: "production_create",
            },
        },

        {
            path: "/productions/edit/:id",
            name: "production_edit",
            component: EditProduction,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Edit Production - ${process.env.MIX_APP_NAME}`,
                gate: "production_edit",
            },
        },

        {
            path: "/productions",
            name: "productions",
            component: Productions,
            beforeEnter: RedirectBasedOnAuthStatus,
            meta: {
                title: `Machines Productions - ${process.env.MIX_APP_NAME}`,
                gate: "production_access",
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
