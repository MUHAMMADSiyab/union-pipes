<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title>New Customer</v-card-title>
                        <v-card-subtitle>Edit Customer</v-card-subtitle>

                        <v-card-text class="mt-1">
                            <v-form @submit.prevent="update">
                                <v-row>
                                    <v-col
                                        xl="3"
                                        lg="3"
                                        md="3"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <v-switch
                                            color="red"
                                            label="Local Customer"
                                            v-model="data.local"
                                        ></v-switch>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col
                                        xl="6"
                                        lg="6"
                                        md="6"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('name')
                                            "
                                        ></small>
                                        <v-text-field
                                            name="customer-name"
                                            label="Customer Name"
                                            id="customer-name"
                                            v-model="data.name"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <v-col
                                        xl="6"
                                        lg="6"
                                        md="6"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('cnic')
                                            "
                                        ></small>
                                        <v-text-field
                                            name="customer-cnic"
                                            label="CNIC"
                                            id="customer-cnic"
                                            v-model="data.cnic"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col
                                        xl="6"
                                        lg="6"
                                        md="6"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('phone')
                                            "
                                        ></small>
                                        <v-text-field
                                            name="customer-phone"
                                            label="Phone"
                                            id="customer-phone"
                                            v-model="data.phone"
                                            dense
                                            outlined
                                        ></v-text-field>
                                    </v-col>

                                    <v-col
                                        xl="6"
                                        lg="6"
                                        md="6"
                                        sm="12"
                                        cols="12"
                                        class="py-0"
                                    >
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('photo')
                                            "
                                        ></small>
                                        <v-file-input
                                            name="customer-photo"
                                            label="Photo"
                                            id="customer-photo"
                                            @change="handleFile"
                                            prepend-inner-icon="mdi-camera"
                                            prepend-icon=""
                                            dense
                                            outlined
                                            hint="Only image files | Max. size 2MB"
                                            :clearable="false"
                                        ></v-file-input>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" class="py-0">
                                        <small
                                            class="red--text"
                                            v-if="validation.hasErrors()"
                                            v-text="
                                                validation.getMessage('address')
                                            "
                                        ></small>
                                        <v-textarea
                                            rows="2"
                                            name="customer-address"
                                            label="Address"
                                            id="customer-address"
                                            v-model="data.address"
                                            dense
                                            outlined
                                        ></v-textarea>
                                    </v-col>
                                </v-row>

                                <v-btn color="success" type="submit"
                                    >Update</v-btn
                                >
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
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
                id: "",
                name: "",
                cnic: "",
                phone: "",
                address: "",
                photo: "",
                local: false,
            },
        };
    },

    methods: {
        ...mapActions({
            getCustomer: "customer/getCustomer",
            updateCustomer: "customer/updateCustomer",
        }),

        handleFile(file) {
            this.data.photo = file;
        },

        async update() {
            this.formLoading = true;

            await this.updateCustomer(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                // Clear the validation messages object
                this.validation.setMessages({});
                return this.$router.push({ name: "customers" });
            }
        },
    },

    computed: {
        ...mapGetters({
            customer: "customer/customer",
            validationErrors: "validationErrors",
        }),
    },

    async mounted() {
        await this.getCustomer(this.$route.params.id);

        if (!this.customer) {
            return this.$router.push({ name: "not_found" });
        }

        this.data.id = this.customer.id;
        this.data.name = this.customer.name;
        this.data.local = this.customer.local;
        this.data.cnic = this.customer.cnic;
        this.data.phone = this.customer.phone;
        this.data.address = this.customer.address;
    },
};
</script>
