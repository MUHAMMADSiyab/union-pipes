<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title>New Partner</v-card-title>
                        <v-card-subtitle>Edit Partner</v-card-subtitle>

                        <v-card-text class="mt-1">
                            <v-form @submit.prevent="update">
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
                                            name="partner-name"
                                            label="Partner Name"
                                            id="partner-name"
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
                                            name="partner-cnic"
                                            label="CNIC"
                                            id="partner-cnic"
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
                                            name="partner-phone"
                                            label="Phone"
                                            id="partner-phone"
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
                                            name="partner-photo"
                                            label="Photo"
                                            id="partner-photo"
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
                                            name="partner-address"
                                            label="Address"
                                            id="partner-address"
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
            },
        };
    },

    methods: {
        ...mapActions({
            getPartner: "partner/getPartner",
            updatePartner: "partner/updatePartner",
        }),

        handleFile(file) {
            this.data.photo = file;
        },

        async update() {
            this.formLoading = true;

            await this.updatePartner(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                // Clear the validation messages object
                this.validation.setMessages({});
                return this.$router.push({ name: "partners" });
            }
        },
    },

    computed: {
        ...mapGetters({
            partner: "partner/partner",
            validationErrors: "validationErrors",
        }),
    },

    async mounted() {
        await this.getPartner(this.$route.params.id);

        if (!this.partner) {
            return this.$router.push({ name: "not_found" });
        }

        this.data.id = this.partner.id;
        this.data.name = this.partner.name;
        this.data.cnic = this.partner.cnic;
        this.data.phone = this.partner.phone;
        this.data.address = this.partner.address;
    },
};
</script>
