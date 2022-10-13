<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>New Meter</v-card-title>
            <v-card-subtitle>Add a New Meter</v-card-subtitle>

            <v-card-text class="mt-1">
              <v-form @submit.prevent="add">
                <v-row>
                  <v-col cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('name')"
                    ></small>
                    <v-text-field
                      v-model="data.name"
                      label="Name"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('dispenser_id')"
                    ></small>
                    <v-select
                      :items="dispensers"
                      item-text="name"
                      item-value="id"
                      v-model="data.dispenser_id"
                      placeholder="Select Dispenser"
                      autocomplete
                      dense
                      outlined
                    ></v-select>
                  </v-col>

                  <v-col cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('code')"
                    ></small>
                    <v-text-field
                      v-model="data.code"
                      label="Code"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('description')"
                    ></small>
                    <v-textarea
                      rows="3"
                      name="description"
                      label="Description"
                      id="description"
                      v-model="data.description"
                      dense
                      outlined
                    ></v-textarea>
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
        name: "",
        dispenser_id: "",
        code: "",
        description: "",
      },
    };
  },

  methods: {
    ...mapActions({
      getDispensers: "dispenser/getDispensers",
      addMeter: "meter/addMeter",
    }),

    async add() {
      this.formLoading = true;

      await this.addMeter(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.name = "";
        this.data.dispenser_id = "";
        this.data.code = "";
        this.data.description = "";
        // Clear the validation messages object
        this.validation.setMessages({});
      }
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
      dispensers: "dispenser/dispensers",
    }),
  },

  mounted() {
    this.getDispensers();
  },
};
</script>