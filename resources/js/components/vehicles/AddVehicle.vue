<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>New Vehicle</v-card-title>
            <v-card-subtitle>Add a New Vehicle</v-card-subtitle>

            <v-card-text class="mt-1">
              <v-form @submit.prevent="add">
                <v-row>
                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('regitration_no')"
                    ></small>
                    <v-text-field
                      v-model="data.registration_no"
                      label="Registration No."
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('chassis_no')"
                    ></small>
                    <v-text-field
                      v-model="data.chassis_no"
                      label="Chassis No."
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('engine_no')"
                    ></small>
                    <v-text-field
                      v-model="data.engine_no"
                      label="Engine No."
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('make')"
                    ></small>
                    <v-text-field
                      v-model="data.make"
                      type="text"
                      label="Make"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('model')"
                    ></small>
                    <v-text-field
                      v-model="data.model"
                      type="text"
                      label="Model"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('receipt_no')"
                    ></small>
                    <v-text-field
                      v-model="data.receipt_no"
                      type="text"
                      label="Receipt No."
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('contractor_name')"
                    ></small>
                    <v-text-field
                      v-model="data.contractor_name"
                      type="text"
                      label="Contractor Name"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('validity')"
                    ></small>
                    <v-menu max-width="290px" min-width="auto">
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="data.validity"
                          v-on="on"
                          label="Validity"
                          prepend-inner-icon="mdi-calendar"
                          dense
                          outlined
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="data.validity"
                        label="Validity"
                        no-title
                        outlined
                        dense
                        show-current
                      ></v-date-picker>
                    </v-menu>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('calibration_date')"
                    ></small>
                    <v-menu max-width="290px" min-width="auto">
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="data.calibration_date"
                          v-on="on"
                          label="Calibration Date"
                          prepend-inner-icon="mdi-calendar"
                          dense
                          outlined
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="data.calibration_date"
                        label="Calibration Date"
                        no-title
                        outlined
                        dense
                        show-current
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                </v-row>

                <v-row>
                  <v-col xl="3" lg="3" md="3" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('no_of_chambers')"
                    ></small>
                    <v-text-field
                      v-model="data.no_of_chambers"
                      type="number"
                      label="# Chambers"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="3" lg="3" md="3" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('capacity')"
                    ></small>
                    <v-text-field
                      v-model="data.capacity"
                      type="number"
                      label="Capacity"
                      :disabled="data.no_of_chambers == 0"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="6" lg="6" md="6" sm="12" cols="12" class="py-0">
                    <h3 class="ml-3 mb-4">Chambers Details</h3>

                    <!-- Chambers -->
                    <v-list
                      v-for="(chamber, i) in data.chambers"
                      :key="i"
                      no-action
                    >
                      <v-list-item>
                        <v-row>
                          <v-col
                            xl="4"
                            lg="4"
                            md="4"
                            sm="12"
                            cols="12"
                            class="py-0"
                          >
                            <v-text-field
                              :label="`Chamber # ${i + 1}`"
                              dense
                              filled
                              disabled
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
                                validation.getMessage(`chambers.${i}.capacity`)
                              "
                            ></small>
                            <v-text-field
                              v-model="chamber.capacity"
                              type="number"
                              label="Capacity"
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
                              v-if="validation.hasErrors()"
                              v-text="
                                validation.getMessage(`chambers.${i}.dip_value`)
                              "
                            ></small>
                            <v-text-field
                              v-model="chamber.dip_value"
                              type="number"
                              label="Dip Value"
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

                <v-btn color="primary" type="submit">Add</v-btn>
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

  components: {
    Navbar,
  },

  data() {
    return {
      formLoading: false,
      data: {
        registration_no: "",
        make: "",
        model: "",
        chassis_no: "",
        engine_no: "",
        contractor_name: "",
        calibration_date: "",
        receipt_no: "",
        validity: "",
        capacity: "",
        no_of_chambers: 1,
        chambers: [{ capacity: 0, dip_value: 0 }],
      },
    };
  },

  methods: {
    ...mapActions({
      addVehicle: "vehicle/addVehicle",
    }),

    calculateChamberCapacity(capacity) {
      this.data.chambers.forEach((chamber) => {
        chamber.capacity = Math.round(capacity / this.data.no_of_chambers);
      });
    },

    async add() {
      this.formLoading = true;

      await this.addVehicle(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.registration_no = "";
        this.data.engine_no = "";
        this.data.chassis_no = "";
        this.data.make = "";
        this.data.model = "";
        this.data.contractor_name = "";
        this.data.calibration_date = "";
        this.data.validity = "";
        this.data.capacity = "";
        this.data.receipt_no = "";
        // Clear the validation messages object
        this.validation.setMessages({});
      }
    },
  },

  watch: {
    "data.no_of_chambers": {
      handler(no_of_chambers) {
        const array = Array.from(Array(parseInt(no_of_chambers)));
        const chambers = [];

        array.forEach((item, index) => {
          chambers.push({
            capacity: this.data.chambers[index]
              ? this.data.chambers[index].capacity
              : 0,
            dip_value: this.data.chambers[index]
              ? this.data.chambers[index].dip_value
              : 0,
          });
        });

        this.data.chambers = chambers;

        this.calculateChamberCapacity(this.data.capacity);
      },
    },

    "data.capacity": {
      handler(newCapacity) {
        this.calculateChamberCapacity(newCapacity);
      },
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
    }),
  },
};
</script>