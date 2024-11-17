import Vue from "vue";
import Vuex from "vuex";

// Modules
import common from "./modules/common";
import print from "./modules/print";
import auth from "./modules/auth";
import alert from "./modules/alert";
import permission from "./modules/permission";
import role from "./modules/role";
import user from "./modules/user";
import setting from "./modules/setting";
import product from "./modules/product";
import tank from "./modules/tank";
import dispenser from "./modules/dispenser";
import bank from "./modules/bank";
import expense_source from "./modules/expense_source";
import expense from "./modules/expense";
import employee from "./modules/employee";
import salary from "./modules/salary";
import company from "./modules/company";
import customer from "./modules/customer";
import purchase_item from "./modules/purchase_item";
import purchase from "./modules/purchase";
import sell from "./modules/sell";
import transaction from "./modules/transaction";
import report from "./modules/report";
import gate_pass from "./modules/gate_pass";
import stock_item from "./modules/stock_item";
import stock from "./modules/stock";
import machine from "./modules/machine";
import production from "./modules/production";
import payment from "./modules/payment";
import dashboard from "./modules/dashboard";
import bulk_payment from "./modules/bulk_payment";
import raw_material from "./modules/raw_material";
import stock_sheet from "./modules/stock_sheet";
import monthly_sheet from "./modules/monthly_sheet";

// Subscriber
import subscriber from "./subscriber";

Vue.use(Vuex);

const store = new Vuex.Store({
    modules: {
        common,
        print,
        auth: {
            ...auth,
            namespaced: true,
        },

        alert: {
            ...alert,
            namespaced: true,
        },

        permission: {
            ...permission,
            namespaced: true,
        },

        role: {
            ...role,
            namespaced: true,
        },

        user: {
            ...user,
            namespaced: true,
        },

        product: {
            ...product,
            namespaced: true,
        },

        bank: {
            ...bank,
            namespaced: true,
        },

        expense_source: {
            ...expense_source,
            namespaced: true,
        },

        expense: {
            ...expense,
            namespaced: true,
        },

        employee: {
            ...employee,
            namespaced: true,
        },

        salary: {
            ...salary,
            namespaced: true,
        },

        company: {
            ...company,
            namespaced: true,
        },

        customer: {
            ...customer,
            namespaced: true,
        },

        purchase_item: {
            ...purchase_item,
            namespaced: true,
        },

        report: {
            ...report,
            namespaced: true,
        },

        purchase: {
            ...purchase,
            namespaced: true,
        },

        sell: {
            ...sell,
            namespaced: true,
        },

        transaction: {
            ...transaction,
            namespaced: true,
        },

        gate_pass: {
            ...gate_pass,
            namespaced: true,
        },

        stock_item: {
            ...stock_item,
            namespaced: true,
        },

        stock: {
            ...stock,
            namespaced: true,
        },

        machine: {
            ...machine,
            namespaced: true,
        },

        production: {
            ...production,
            namespaced: true,
        },

        payment: {
            ...payment,
            namespaced: true,
        },

        setting: {
            ...setting,
            namespaced: true,
        },

        dashboard: {
            ...dashboard,
            namespaced: true,
        },

        bulk_payment: {
            ...bulk_payment,
            namespaced: true,
        },

        raw_material: {
            ...raw_material,
            namespaced: true,
        },

        stock_sheet: {
            ...stock_sheet,
            namespaced: true,
        },

        monthly_sheet: {
            ...monthly_sheet,
            namespaced: true,
        },
    },
});

// Attach a subscriber which will take care of attaching the token to every auth request
subscriber(store);

export default store;
