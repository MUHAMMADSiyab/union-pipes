<template>
    <div>
        <Navbar v-if="!printMode" />

        <v-container class="mt-4">
            <v-row>
                <v-col cols="12">
                    <v-card :loading="formLoading" :disabled="formLoading">
                        <v-card-title primary-title>New Gate Pass</v-card-title>
                        <v-card-subtitle
                            >Create a New Gate Pass</v-card-subtitle
                        >

                        <v-card-text class="mt-2">
                            <v-form @submit.prevent="add">
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
                                                validation.getMessage(
                                                    'receiver'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            name="receiver"
                                            label="Receiver"
                                            id="receiver"
                                            v-model="data.receiver"
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
                                                    'vehicle_no'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            name="vehicle_no"
                                            label="Vehicle No."
                                            id="vehicle_no"
                                            v-model="data.vehicle_no"
                                            dense
                                            outlined
                                        ></v-text-field>
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
                                                    'driver_name'
                                                )
                                            "
                                        ></small>
                                        <v-text-field
                                            name="driver_name"
                                            label="Driver Name"
                                            id="driver_name"
                                            v-model="data.driver_name"
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
                                                validation.getMessage('in_time')
                                            "
                                        ></small>
                                        <v-menu
                                            transition="scale-transition"
                                            offset-y
                                            max-width="290px"
                                            min-width="290px"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    v-model="data.in_time"
                                                    v-on="on"
                                                    label="In Time"
                                                    prepend-inner-icon="mdi-calendar"
                                                    dense
                                                    outlined
                                                ></v-text-field>
                                            </template>
                                            <v-time-picker
                                                :close-on-content-click="false"
                                                v-model="data.in_time"
                                                label="In Time"
                                                :ampm-in-title="true"
                                                format="ampm"
                                                outlined
                                                dense
                                                show-current
                                            ></v-time-picker>
                                        </v-menu>
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
                                                validation.getMessage('in_time')
                                            "
                                        ></small>
                                        <v-menu
                                            transition="scale-transition"
                                            offset-y
                                            max-width="290px"
                                            min-width="290px"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                    v-model="data.out_time"
                                                    v-on="on"
                                                    label="Out Time"
                                                    prepend-inner-icon="mdi-calendar"
                                                    dense
                                                    outlined
                                                ></v-text-field>
                                            </template>
                                            <v-time-picker
                                                v-model="data.out_time"
                                                label="Out Time"
                                                :ampm-in-title="true"
                                                format="ampm"
                                                outlined
                                                dense
                                                show-current
                                            ></v-time-picker>
                                        </v-menu>
                                    </v-col>
                                </v-row>

                                <!-- Items -->
                                <v-row class="mt-2">
                                    <v-col cols="12" class="py-0">
                                        <h3 class="ml-3 mb-4">Items</h3>

                                        <v-btn
                                            color="success"
                                            x-small
                                            @click="addItemRow"
                                            class="mb-3 ml-3"
                                            ><v-icon x-small
                                                >mdi-plus</v-icon
                                            ></v-btn
                                        >

                                        <v-list
                                            v-for="(item, i) in data.items"
                                            dense
                                            :key="i"
                                            no-action
                                        >
                                            <v-list-item>
                                                <v-btn
                                                    color="error"
                                                    x-small
                                                    @click="removeItemRow(i)"
                                                    class="mb-3 mr-2"
                                                    ><v-icon x-small
                                                        >mdi-minus</v-icon
                                                    ></v-btn
                                                >
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
                                                            v-if="
                                                                validation.hasErrors()
                                                            "
                                                            v-text="
                                                                validation.getMessage(
                                                                    `items.${i}.particular`
                                                                )
                                                            "
                                                        ></small>
                                                        <v-text-field
                                                            v-model="
                                                                item.particular
                                                            "
                                                            label="Particular"
                                                            dense
                                                            filled
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
                                                            v-if="
                                                                validation.hasErrors()
                                                            "
                                                            v-text="
                                                                validation.getMessage(
                                                                    `items.${i}.quantity`
                                                                )
                                                            "
                                                        ></small>
                                                        <v-text-field
                                                            v-model="
                                                                item.quantity
                                                            "
                                                            type="number"
                                                            label="Quantity"
                                                            dense
                                                            filled
                                                        ></v-text-field>
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
                                                            v-if="
                                                                validation.hasErrors()
                                                            "
                                                            v-text="
                                                                validation.getMessage(
                                                                    `items.${i}.remarks`
                                                                )
                                                            "
                                                        ></small>
                                                        <v-text-field
                                                            v-model="
                                                                item.remarks
                                                            "
                                                            label="Remarks"
                                                            dense
                                                            filled
                                                        ></v-text-field>
                                                    </v-col>
                                                </v-row>
                                            </v-list-item>
                                            <v-divider class="mb-3"></v-divider>
                                        </v-list>
                                    </v-col>
                                </v-row>

                                <v-btn color="primary" type="submit"
                                    >Create Gate Pass</v-btn
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
                receiver: "",
                date: "",
                in_time: "",
                out_time: "",
                driver_name: "",
                vehicle_no: "",
                items: [{ particular: "", quantity: 0, remarks: "" }],
            },
        };
    },

    methods: {
        ...mapActions({
            addGatePass: "gate_pass/addGatePass",
        }),

        addItemRow() {
            this.data.items = [
                ...this.data.items,
                {
                    particular: "",
                    quantity: 0,
                    remarks: "",
                },
            ];
        },

        removeItemRow(index) {
            this.data.items = this.data.items.filter((item, i) => i !== index);
        },

        async add() {
            this.formLoading = true;

            await this.addGatePass(this.data);

            this.formLoading = false;

            // Validation
            if (this.validationErrors !== null) {
                this.validation.setMessages(this.validationErrors.errors);
            } else {
                this.data.date = "";
                this.data.receiver = "";
                this.data.vehicle_no = "";
                this.data.driver_name = "";
                this.data.in_time = "";
                this.data.out_time = "";
                this.data.items = [
                    { particular: "", quantity: 0, remarks: "" },
                ];

                // Clear the validation messages object
                this.validation.setMessages({});

                if (this.new_gate_pass) {
                    this.$router.push({
                        name: "gate_pass_show",
                        params: { id: this.new_gate_pass.id },
                    });
                }
            }
        },
    },

    computed: {
        ...mapGetters({
            new_gate_pass: "gate_pass/new_gate_pass",
            loading: "loading",
            validationErrors: "validationErrors",
        }),
    },
};
</script>
