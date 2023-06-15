<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Customers</h5>

            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-text-field
                                v-model="searchKeyword"
                                label="Search by Customer Name"
                                outlined
                                dense
                            ></v-text-field>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-switch
                        color="red"
                        v-model="local"
                        label="Local Customers"
                        @change="handleLocalSwitch"
                    ></v-switch>
                </v-col>
            </v-row>

            <!-- Permanent Customers -->
            <v-row v-if="!local">
                <v-col
                    v-for="(customer, index) in customers"
                    :key="index"
                    cols="12"
                    sm="6"
                    md="4"
                    lg="3"
                    xl="2"
                >
                    <div
                        class="d-flex flex-column align-center pa-2"
                        style="background-color: #fff; border-radius: 8px"
                    >
                        <div class="mb-2">
                            <v-avatar
                                size="80"
                                color="grey"
                                class="white--text"
                            >
                                <v-img :src="customer.photo" contain></v-img>
                            </v-avatar>
                        </div>
                        <div class="text-center mb-2">
                            <div class="font-weight-bold">
                                {{ customer.name }}
                            </div>
                            <div
                                class="text--secondary mt-1"
                                style="font-size: 12px"
                            >
                                CNIC: {{ customer.cnic }}
                            </div>
                            <div
                                class="text--secondary"
                                style="font-size: 12px"
                            >
                                Phone: {{ customer.phone }}
                            </div>
                        </div>
                        <div class="d-flex justify-center">
                            <v-btn
                                small
                                color="primary"
                                icon
                                :to="`/customers/edit/${customer.id}`"
                                title="Edit"
                                v-if="can('customer_edit')"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn
                                small
                                color="error"
                                icon
                                @click="setCustomerId(customer.id)"
                                title="Delete"
                                v-if="can('customer_delete')"
                            >
                                <v-icon small>mdi-delete</v-icon>
                            </v-btn>
                            <v-btn
                                small
                                color="info darken-2"
                                icon
                                :to="`/customers/${customer.id}/ledger_entries`"
                                title="Ledger Entries"
                            >
                                <v-icon small>mdi-account-cash-outline</v-icon>
                            </v-btn>
                        </div>
                    </div>
                </v-col>
            </v-row>

            <!-- Local Customers -->
            <v-card v-else>
                <v-card-text>
                    <v-simple-table dense>
                        <template v-slot:default>
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>CNIC</th>
                                    <th>Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="customer in customers"
                                    :key="customer.cnic"
                                >
                                    <td>
                                        <img
                                            :src="customer.photo"
                                            alt="Customer Photo"
                                            class="mt-2"
                                            width="50"
                                            height="50"
                                        />
                                    </td>
                                    <td>{{ customer.name }}</td>
                                    <td>{{ customer.phone }}</td>
                                    <td>{{ customer.cnic }}</td>
                                    <td>{{ customer.address }}</td>
                                    <td>
                                        <v-btn
                                            x-small
                                            text
                                            color="secondary"
                                            :to="`/customers/edit/${customer.id}`"
                                            title="Edit"
                                            v-if="can('customer_edit')"
                                        >
                                            <v-icon small>mdi-pencil</v-icon>
                                        </v-btn>
                                        <v-btn
                                            x-small
                                            text
                                            color="red darken-2"
                                            @click="setCustomerId(customer.id)"
                                            title="Delete"
                                            v-if="can('customer_delete')"
                                        >
                                            <v-icon small>mdi-delete</v-icon>
                                        </v-btn>

                                        <v-btn
                                            x-small
                                            text
                                            color="info darken-2"
                                            :to="`/customers/${customer.id}/ledger_entries`"
                                            title="Ledger Entries"
                                        >
                                            <v-icon small
                                                >mdi-account-cash-outline</v-icon
                                            >
                                        </v-btn>
                                    </td>
                                </tr>
                            </tbody>
                        </template>
                    </v-simple-table>
                </v-card-text>
            </v-card>

            <!-- Confirmation -->
            <Confirmation
                ref="confirmationComponent"
                :id="customerId"
                @confirmDeletion="
                    customerId
                        ? handleCustomerDelete()
                        : handleMultipleCustomersDelete()
                "
            />

            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import EditCustomer from "./EditCustomer.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";

export default {
    components: {
        EditCustomer,
        Navbar,
        Confirmation,
    },

    data() {
        return {
            searchKeyword: "",
            customerId: null,
            local: false,
        };
    },

    methods: {
        ...mapActions({
            getCustomers: "customer/getCustomers",
            searchCustomers: "customer/searchCustomers",
            getCustomer: "customer/getCustomer",
            deleteCustomer: "customer/deleteCustomer",
            deleteMultipleCustomers: "customer/deleteMultipleCustomers",
        }),

        handleLocalSwitch(local) {
            this.getCustomers(local);
        },

        setCustomerId(id) {
            this.customerId = id;
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleCustomerDelete() {
            await this.deleteCustomer(this.customerId);
            this.customerId = null;
            this.$refs.confirmationComponent.setDialog(false);
        },

        async deleteMultiple() {
            this.$refs.confirmationComponent.setDialog(true);
        },

        async handleMultipleCustomersDelete() {
            const ids = this.selectedItems.map(
                (selectedItem) => selectedItem.id
            );
            await this.deleteMultipleCustomers(ids);

            this.$refs.confirmationComponent.setDialog(false);
            this.selectedItems = [];
        },
    },

    computed: {
        ...mapGetters({
            customers: "customer/customers",
            customer: "customer/customer",
            loading: "loading",
        }),
    },

    watch: {
        searchKeyword: {
            handler(searchValue) {
                this.searchCustomers({
                    searchKeyword: searchValue,
                    local: this.local,
                });
            },
        },
    },

    mounted() {
        this.getCustomers(this.local);
    },
};
</script>

<style scoped>
.v-avatar {
    border-radius: 50%;
}
</style>
