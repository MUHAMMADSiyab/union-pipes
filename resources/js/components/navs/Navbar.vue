<template>
    <nav>
        <v-toolbar flat dense>
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

            <v-app-bar-title>
                <v-img
                    v-if="app_setting"
                    :src="app_setting.app_logo"
                    max-height="40"
                    max-width="80"
                    class="mx-auto"
                ></v-img>
            </v-app-bar-title>

            <v-spacer></v-spacer>

            <!-- dark mode -->
            <v-checkbox
                class="mt-5 pt-1"
                v-model="darkMode"
                color="orange"
                :hint="`Dark Mode: ${darkMode ? 'On' : 'Off'}`"
                off-icon="mdi-theme-light-dark"
                on-icon="mdi-theme-light-dark"
            ></v-checkbox>

            <v-menu offset-y>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        class="mr-1"
                        text
                        plain
                        small
                        v-bind="attrs"
                        v-on="on"
                    >
                        <v-icon>mdi-account-outline</v-icon>
                        {{ authUser && authUser.name }}
                    </v-btn>
                </template>
                <v-list dense>
                    <v-list-item to="/edit_user_account" link>
                        <v-list-item-title
                            ><v-icon left>mdi-account-edit</v-icon> Edit
                            Account</v-list-item-title
                        >
                    </v-list-item>
                    <v-list-item to="/settings" link>
                        <v-list-item-title
                            ><v-icon left>mdi-cog</v-icon> Edit App
                            Setting</v-list-item-title
                        >
                    </v-list-item>
                </v-list>
            </v-menu>

            <v-btn class="mr-1" text plain small @click="logout">
                <v-icon>mdi-logout</v-icon>
                Logout
            </v-btn>
        </v-toolbar>

        <v-navigation-drawer v-model="drawer" app color="primary" dark>
            <!-- eslint-disable vue/no-use-v-if-with-v-for -->
            <v-list dense>
                <!-- Header -->
                <v-list-item-title
                    v-if="app_setting"
                    class="text-h6 text-center mb-3 lighter--text"
                >
                    {{ app_setting.app_name }}
                </v-list-item-title>

                <v-divider></v-divider>

                <v-list-item
                    v-for="link in singleLinks"
                    :key="link.text"
                    link
                    :to="link.to"
                    v-if="!link.gate ? true : can(link.gate)"
                    :exact="link.exact"
                >
                    <v-list-item-icon
                        ><v-icon>{{ link.icon }}</v-icon></v-list-item-icon
                    >
                    <v-list-item-content>
                        <v-list-item-title>{{ link.text }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-group
                    v-for="(link, index) in nestedLinks"
                    :key="index"
                    v-model="link.active"
                    :prepend-icon="link.icon"
                    class="font-weight-bold"
                    v-if="!link.gate ? true : can(link.gate)"
                    no-action
                    color="lighter"
                >
                    <template v-slot:activator>
                        <v-list-item-content>
                            <v-list-item-title>{{
                                link.text
                            }}</v-list-item-title>
                        </v-list-item-content>
                    </template>

                    <!-- Sublist -->
                    <v-list-item
                        :close-on-content-click="false"
                        v-for="(subLink, i) in link.submenu"
                        :key="i"
                        link
                        :to="subLink.to"
                        v-if="!subLink.gate ? true : can(subLink.gate)"
                        :exact="subLink.exact"
                    >
                        <v-list-item-content>
                            <v-list-item-title
                                ><v-icon small>{{ subLink.icon }}</v-icon>
                                {{ subLink.text }}</v-list-item-title
                            >
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>
            </v-list>
        </v-navigation-drawer>
    </nav>
</template>

<script>
import { mapActions, mapGetters } from "vuex";

export default {
    data() {
        return {
            drawer: true,
            darkMode: false,
            navLinks: [
                {
                    text: "Dashboard",
                    icon: "mdi-view-dashboard",
                    to: "/",
                    active: this.activeMenu("/"),
                },

                {
                    text: "Products",
                    icon: "mdi-format-list-bulleted",
                    active: this.activeMenu("products"),
                    gate: "product_access",
                    submenu: [
                        {
                            text: "Add Product",
                            to: "/products/add",
                            icon: "mdi-chevron-double-right",
                            gate: "product_create",
                            exact: true,
                        },
                        {
                            text: "Manage Products",
                            to: "/products",
                            icon: "mdi-chevron-double-right",
                            gate: "product_access",
                            exact: true,
                        },
                    ],
                },

                {
                    text: "Expenses",
                    icon: "mdi-currency-usd",
                    active:
                        this.activeMenu("expenses") ||
                        this.activeMenu("expense_sources"),
                    gate: "expense_access",
                    submenu: [
                        {
                            text: "Expense Sources",
                            to: "/expense_sources",
                            icon: "mdi-chevron-double-right",
                            gate: "expense_access",
                            exact: true,
                        },
                        {
                            text: "Add Expense",
                            to: "/expenses/add",
                            icon: "mdi-chevron-double-right",
                            gate: "expense_create",
                            exact: true,
                        },
                        {
                            text: "Manage Expenses",
                            to: "/expenses",
                            icon: "mdi-chevron-double-right",
                            gate: "expense_access",
                            exact: true,
                        },
                    ],
                },

                {
                    text: "Supplier Companies",
                    icon: "mdi-office-building-outline",
                    active: this.activeMenu("companies"),
                    gate: "company_access",
                    submenu: [
                        {
                            text: "Add Company",
                            to: "/companies/add",
                            icon: "mdi-chevron-double-right",
                            gate: "company_create",
                            exact: true,
                        },
                        {
                            text: "Manage Companies",
                            to: "/companies",
                            icon: "mdi-chevron-double-right",
                            gate: "company_access",
                            exact: true,
                        },
                    ],
                },

                {
                    text: "Purchases",
                    icon: "mdi-archive-arrow-down-outline",
                    active:
                        this.activeMenu("purchases") ||
                        this.activeMenu("purchase_items"),
                    gate: "purchase_access",
                    submenu: [
                        {
                            text: "Purchase Items",
                            to: "/purchase_items",
                            icon: "mdi-chevron-double-right",
                            gate: "purchase_item_access",
                            exact: true,
                        },
                        {
                            text: "Add Purchase",
                            to: "/purchases/add",
                            icon: "mdi-chevron-double-right",
                            gate: "purchase_create",
                            exact: true,
                        },
                        {
                            text: "Manage Purchases",
                            to: "/purchases",
                            icon: "mdi-chevron-double-right",
                            gate: "purchase_access",
                            exact: true,
                        },
                    ],
                },

                {
                    text: "Sells",
                    icon: "mdi-archive-arrow-up-outline",
                    active: this.activeMenu("sells"),
                    gate: "sell_access",
                    submenu: [
                        {
                            text: "Add Sell",
                            to: "/sells/add",
                            icon: "mdi-chevron-double-right",
                            gate: "sell_create",
                            exact: true,
                        },
                        {
                            text: "Manage Sells",
                            to: "/sells",
                            icon: "mdi-chevron-double-right",
                            gate: "sell_access",
                            exact: true,
                        },
                    ],
                },

                {
                    text: "Customers",
                    icon: "mdi-account-group-outline",
                    active: this.activeMenu("customers"),
                    gate: "customer_access",
                    submenu: [
                        {
                            text: "Add Customer",
                            to: "/customers/add",
                            icon: "mdi-chevron-double-right",
                            gate: "customer_create",
                            exact: true,
                        },
                        {
                            text: "Manage Customers",
                            to: "/customers",
                            icon: "mdi-chevron-double-right",
                            gate: "customer_access",
                            exact: true,
                        },
                    ],
                },

                {
                    text: "Employees",
                    icon: "mdi-account-box-multiple",
                    active:
                        this.activeMenu("employees") ||
                        this.activeMenu("salaries"),
                    gate: "employee_access",
                    submenu: [
                        {
                            text: "Add Employee",
                            to: "/employees/add",
                            icon: "mdi-chevron-double-right",
                            gate: "employee_create",
                            exact: true,
                        },
                        {
                            text: "Manage Employees",
                            to: "/employees",
                            icon: "mdi-chevron-double-right",
                            gate: "employee_access",
                            exact: true,
                        },
                    ],
                },

                {
                    text: "Banks",
                    icon: "mdi-bank",
                    active: this.activeMenu("banks"),
                    gate: "bank_access",
                    submenu: [
                        {
                            text: "Add Bank",
                            to: "/banks/add",
                            icon: "mdi-chevron-double-right",
                            gate: "bank_create",
                            exact: true,
                        },
                        {
                            text: "Manage Banks",
                            to: "/banks",
                            icon: "mdi-chevron-double-right",
                            gate: "bank_access",
                            exact: true,
                        },
                    ],
                },

                {
                    text: "Transactions",
                    icon: "mdi-currency-eur",
                    active: this.activeMenu("transactions"),
                    gate: "transaction_access",
                    submenu: [
                        {
                            text: "Add Transaction",
                            to: "/transactions/add",
                            icon: "mdi-chevron-double-right",
                            gate: "transaction_create",
                            exact: true,
                        },
                        {
                            text: "Manage Transactions",
                            to: "/transactions",
                            icon: "mdi-chevron-double-right",
                            gate: "transaction_access",
                            exact: true,
                        },
                    ],
                },

                {
                    text: "Gate Passes",
                    icon: "mdi-receipt",
                    active: this.activeMenu("gate_passes"),
                    gate: "gate_pass_access",
                    submenu: [
                        {
                            text: "Add Gate Pass",
                            to: "/gate_passes/add",
                            icon: "mdi-chevron-double-right",
                            gate: "gate_pass_create",
                            exact: true,
                        },
                        {
                            text: "Manage Gate Passes",
                            to: "/gate_passes",
                            icon: "mdi-chevron-double-right",
                            gate: "gate_pass_access",
                            exact: true,
                        },
                    ],
                },

                {
                    text: "Stock Items",
                    icon: "mdi-warehouse",
                    active: this.activeMenu("stock_items"),
                    gate: "stock_item_access",
                    submenu: [
                        {
                            text: "Add Stock Item",
                            to: "/stock_items/add",
                            icon: "mdi-chevron-double-right",
                            gate: "stock_item_create",
                            exact: true,
                        },
                        {
                            text: "Manage Stock Items",
                            to: "/stock_items",
                            icon: "mdi-chevron-double-right",
                            gate: "stock_item_access",
                            exact: true,
                        },
                    ],
                },

                {
                    text: "Reports",
                    icon: "mdi-file-document-multiple-outline",
                    active: this.activeMenu("reports"),
                    gate: "report_access",
                    submenu: [
                        {
                            text: "Expense",
                            to: "/reports/expense",
                            icon: "mdi-chevron-double-right",
                            gate: "report_access",
                            exact: true,
                        },

                        {
                            text: "Purchase",
                            to: "/reports/purchase",
                            icon: "mdi-chevron-double-right",
                            gate: "report_access",
                            exact: true,
                        },

                        {
                            text: "Purchased Items",
                            to: "/reports/purchased_items",
                            icon: "mdi-chevron-double-right",
                            gate: "report_access",
                            exact: true,
                        },

                        {
                            text: "Sell",
                            to: "/reports/sell",
                            icon: "mdi-chevron-double-right",
                            gate: "report_access",
                            exact: true,
                        },

                        {
                            text: "Sold Items",
                            to: "/reports/sold_items",
                            icon: "mdi-chevron-double-right",
                            gate: "report_access",
                            exact: true,
                        },

                        {
                            text: "Receivables",
                            to: "/reports/receivables",
                            icon: "mdi-chevron-double-right",
                            gate: "report_access",
                            exact: true,
                        },

                        {
                            text: "Payables",
                            to: "/reports/payables",
                            icon: "mdi-chevron-double-right",
                            gate: "report_access",
                            exact: true,
                        },
                    ],
                },

                {
                    text: "Payment Setting",
                    icon: "mdi-currency-usd-circle-outline",
                    to: "/payment_settings",
                    gate: "payment_setting_edit",
                    active: this.activeMenu("payment_setting"),
                },

                {
                    text: "User Management",
                    icon: "mdi-account-group",
                    gate: "user_management_access",
                    active: this.activeMenu("user-management"),
                    submenu: [
                        {
                            text: "Users",
                            to: "/user-management/users",
                            icon: "mdi-chevron-double-right",
                            gate: "user_access",
                        },
                        {
                            text: "Roles",
                            to: "/user-management/roles",
                            icon: "mdi-chevron-double-right",
                            gate: "role_access",
                        },
                        {
                            text: "Permissions",
                            to: "/user-management/permissions",
                            icon: "mdi-chevron-double-right",
                            gate: "permission_access",
                        },
                    ],
                },
            ],
        };
    },

    computed: {
        singleLinks() {
            return this.navLinks.filter((navLink) => !navLink.submenu);
        },

        nestedLinks() {
            return this.navLinks.filter((navLink) => navLink.submenu);
        },

        ...mapGetters({
            authUser: "auth/user",
            app_setting: "setting/app_setting",
        }),
    },

    methods: {
        ...mapActions({
            doLogout: "auth/logout",
            getAppSetting: "setting/getAppSetting",
        }),

        activeMenu(pathName) {
            return this.$route.path.split("/")[1] === pathName;
        },

        async logout() {
            await this.doLogout();

            this.$router.replace({ name: "login" });
        },
    },

    watch: {
        darkMode: {
            handler(darkMode) {
                localStorage.setItem("dark", darkMode);

                this.$vuetify.theme.dark = darkMode;
            },
        },
    },

    mounted() {
        this.getAppSetting();

        this.darkMode = localStorage.getItem("dark") == "true";
    },
};
</script>

<style>
.v-list-group--active.v-application .primary--text {
    color: aquamarine !important;
    font-weight: bold !important;
}
.v-navigation-drawer__content {
    overflow-y: overlay;
}

.v-navigation-drawer__content::-webkit-scrollbar-track {
    background-color: #1a68d2;
}

.v-navigation-drawer__content::-webkit-scrollbar {
    width: 0px;
    transition: 0.4s;
}

.v-navigation-drawer__content:hover::-webkit-scrollbar {
    width: 8px;
    cursor: pointer;
}

.v-navigation-drawer__content::-webkit-scrollbar-thumb {
    border-radius: 5px;
    box-shadow: inset 0 0 6px #6ba5f7;
    -webkit-box-shadow: inset 0 0 6px #6ba5f7;
    background-color: #6ba5f7;
}
</style>
