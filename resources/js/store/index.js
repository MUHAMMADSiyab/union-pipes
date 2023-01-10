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
import meter from "./modules/meter";
import vehicle from "./modules/vehicle";
import rate from "./modules/rate";
import bank from "./modules/bank";
import transaction from "./modules/transaction";
import utility from "./modules/utility";
import employee from "./modules/employee";
import salary from "./modules/salary";
import company from "./modules/company";
import purchase from "./modules/purchase";
import sell from "./modules/sell";
import customer from "./modules/customer";
import account from "./modules/account";
import invoice from "./modules/invoice";
import vehicle_transaction from "./modules/vehicle_transaction";
import payment from "./modules/payment";
import dashboard from "./modules/dashboard";

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

        tank: {
            ...tank,
            namespaced: true,
        },

        dispenser: {
            ...dispenser,
            namespaced: true,
        },

        meter: {
            ...meter,
            namespaced: true,
        },

        vehicle: {
            ...vehicle,
            namespaced: true,
        },

        rate: {
            ...rate,
            namespaced: true,
        },

        bank: {
            ...bank,
            namespaced: true,
        },

        transaction: {
            ...transaction,
            namespaced: true,
        },

        utility: {
            ...utility,
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

        purchase: {
            ...purchase,
            namespaced: true,
        },

        sell: {
            ...sell,
            namespaced: true,
        },

        customer: {
            ...customer,
            namespaced: true,
        },

        account: {
            ...account,
            namespaced: true,
        },

        invoice: {
            ...invoice,
            namespaced: true,
        },

        vehicle_transaction: {
            ...vehicle_transaction,
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
    },
});

// Attach a subscriber which will take care of attaching the token to every auth request
subscriber(store);

export default store;
