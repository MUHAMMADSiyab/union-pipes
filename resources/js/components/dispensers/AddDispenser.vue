<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>New Dispenser</v-card-title>
            <v-card-subtitle>Add a New Dispenser</v-card-subtitle>

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
                      name="dispenser-name"
                      label="Dispenser Name"
                      id="dispenser-name"
                      v-model="data.name"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('tank_id')"
                    ></small>
                    <v-select
                      :items="tanks"
                      item-text="name"
                      item-value="id"
                      v-model="data.tank_id"
                      placeholder="Select Tank"
                      autocomplete
                      dense
                      outlined
                    ></v-select>
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

  components: { Navbar },

  data() {
    return {
      formLoading: false,
      data: {
        name: "",
        tank_id: "",
        discription: "",
      },
    };
  },

  methods: {
    ...mapActions({
      addDispenser: "dispenser/addDispenser",
      getTanks: "tank/getTanks",
    }),

    async add() {
      this.formLoading = true;

      await this.addDispenser(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.name = "";
        this.data.tank_id = "";
        this.data.description = "";
        // Clear the validation messages object
        this.validation.setMessages({});
      }
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
      tanks: "tank/tanks",
    }),
  },

  mounted() {
    this.getTanks();
  },
};
</script>