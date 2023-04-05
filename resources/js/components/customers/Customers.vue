<template>
    <div>
        <Navbar v-if="!printMode" />

        <print-button />

        <v-container class="mt-4">
            <h5 class="text-subtitle-1 mb-2">Customers</h5>

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
                    xl="3"
                    lg="3"
                    md="3"
                    sm="12"
                    cols="12"
                    v-for="(customer, i) in customers"
                    :key="i"
                >
                    <v-card class="mx-auto" max-width="344" ripple>
                        <v-img
                            :src="customer.photo"
                            v-if="customer.photo"
                            height="220px"
                        ></v-img>

                        <v-card-title>
                            {{ customer.name }}
                        </v-card-title>

                        <v-card-subtitle v-if="customer.phone">
                            <v-icon left>mdi-card-account-phone-outline</v-icon>
                            {{ customer.phone }}
                        </v-card-subtitle>

                        <v-card-actions>
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
                                <v-icon small>mdi-account-cash-outline</v-icon>
                            </v-btn>
                        </v-card-actions>
                    </v-card>
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
            customerId: null,
            local: false,
        };
    },

    methods: {
        ...mapActions({
            getCustomers: "customer/getCustomers",
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

    mounted() {
        this.getCustomers(this.local);
    },
};
</script>
