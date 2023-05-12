<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title
                            >New Production</v-card-title
                        >
                        <v-card-subtitle>Add a New Production</v-card-subtitle>

                        <v-card-text class="mt-1">
                            <v-form @submit.prevent="add">
                                <v-row>
                                    <v-col
                                        xl="4"
                                        lg="4"
                                        md="4"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'machine_id'
                                                )
                                            "
                                        ></small>
                                        <v-select
                                            :items="machines"
                                            item-text="name"
                                            item-value="id"
                                            v-model="data.machine_id"
                                            placeholder="Select Machine"
                                            autocomplete
                                            dense
                                            outlined
                                        ></v-select>
                                    </v-col>

                                    <v-col
                                        xl="4"
                                        lg="4"
                                        md="4"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'employee_id'
                                                )
                                            "
                                        ></small>
                                        <v-select
                                            :items="employees"
                                            item-text="name"
                                            item-value="id"
                                            v-model="data.employee_id"
                                            placeholder="Select Operator"
                                            autocomplete
                                            dense
                                            outlined
                                        ></v-select>
                                    </v-col>

                                    <v-col
                                        xl="4"
                                        lg="4"
                                        md="4"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'product_id'
                                                )
                                            "
                                        ></small>
                                        <v-select
                                            :items="products"
                                            item-text="name"
                                            item-value="id"
                                            v-model="data.product_id"
                                            placeholder="Select Product"
                                            autocomplete
                                            dense
                                            outlined
                                        ></v-select>
                                    </v-col>

                                    <v-col
                                        xl="3"
                                        lg="3"
                                        md="3"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('date')
                                            "
                                        ></small>
                                        <v-menu
                                            max-width="290px"
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    v-model="data.date"
                                                    v-on="on"
                                                    label="Date"
                                                    prepend-inner-icon="mdi-calendar"
                                                    dense
                                                    outlined
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="data.date"
                                                label="Date"
                                                no-title
                                                outlined
                                                dense
                                                show-current
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>

                                    <v-col
                                        xl="3"
                                        lg="3"
                                        md="3"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('shift')
                                            "
                                        ></small>
                                        <v-text-field
                                            name="shift"
                                            label="Shift"
                                            id="shift"
                                            v-model="data.shift"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <v-col
                                        xl="2"
                                        lg="2"
                                        md="2"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('weight')
                                            "
                                        ></small>
                                        <v-text-field
                                            name="weight"
                                            label="Weight"
                                            id="weight"
                                            v-model="data.weight"
                                            type="number"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <v-col
                                        xl="2"
                                        lg="2"
                                        md="2"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'quantity'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            name="quantity"
                                            label="Quantity"
                                            id="quantity"
                                            v-model="data.quantity"
                                            type="number"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <v-col
                                        xl="2"
                                        lg="2"
                                        md="2"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'total_weight'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            name="total_weight"
                                            label="Total Weight"
                                            id="total_weight"
                                            v-model="data.total_weight"
                                            type="number"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <v-col cols="12" class="py-0">
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage(
                                                    'description'
                                                )
                                            "
                                        ></small>
                                        <v-textarea
                                            name="description"
                                            label="Description"
                                            id="description"
                                            v-model="data.description"
                                            rows="3"
                                            dense
                                            outlined
                                        ></v-textarea>
                                    </v-col>
                                </v-row>

                                <v-btn
                                    color="primary"
                                    class="mt-4"
                                    type="submit"
                                    >Add Production</v-btn
                                >
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <alert />
        </v-container>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar";

export default {
    mixins: [ValidationMixin],

    components: { Navbar },

    data() {
        return {
            formLoading: false,
            data: {
                machine_id: "",
                employee_id: "",
                product_id: "",
                date: "",
                shift: "",
                weight: 0,
                quantity: 0,
                total_weight: 0,
                description: "",
            },
        };
    },

    methods: {
        ...mapActions({
            getMachines: "machine/getMachines",
            getEmployees: "employee/getEmployees",
            getProducts: "product/getProducts",
            addProduction: "production/addProduction",
        }),

        async add() {
            this.formLoading = true;

            await this.addProduction(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.data.machine_id = "";
                this.data.employee_id = "";
                this.data.product_id = "";
                this.data.date = "";
                this.data.shift = "";
                this.data.weight = 0;
                this.data.quantity = 0;
                this.data.total_weight = 0;
                this.data.description = "";
                // Clear the validation messages object
                this.validation.setMessages({});
            }
        },
    },

    computed: {
        ...mapGetters({
            machines: "machine/machines",
            employees: "employee/employees",
            products: "product/products",
            validationErrors: "validationErrors",
        }),
    },

    watch: {
        data: {
            handler(newVal) {
                this.data.total_weight = newVal.weight * newVal.quantity;
            },
            deep: true,
        },
    },

    mounted() {
        Promise.all([
            this.getMachines(),
            this.getEmployees(),
            this.getProducts(),
        ]);
    },
};
</script>
