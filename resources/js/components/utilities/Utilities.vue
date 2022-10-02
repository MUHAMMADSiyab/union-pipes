<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Utilities</h5>

      <v-row>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="utilities"
            class="elevation-1"
            item-key="id"
            :search="search"
            :items-per-page="perPage"
            :loading="loading"
            :show-select="can('utility_delete') && !printMode"
            loading-text="Loading utilities..."
            :footer-props="footerProps"
            v-model="selectedItems"
            dense
          >
            <!-- S# -->
            <template slot="item.sno" slot-scope="props">{{
              props.index + 1
            }}</template>

            <!-- Amount -->
            <template slot="item.amount" slot-scope="props">
              <span> {{ money(props.item.amount) }} </span>
            </template>

            <!-- Cheque Images (Button) -->
            <template
              slot="item.payment.cheque_images"
              slot-scope="props"
              v-if="props.item.payment.cheque_images.length"
            >
              <v-btn
                color="light"
                x-small
                title="Cheque Image(s)"
                @click="
                  setCurrentChequeImages(props.item.payment.cheque_images)
                "
                ><v-icon>mdi-file-image-outline</v-icon></v-btn
              >
            </template>

            <!-- Description -->
            <template
              slot="item.description"
              slot-scope="props"
              v-if="props.item.description"
            >
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <span v-bind="attrs" v-on="on">
                    {{
                      props.item.description &&
                      props.item.description.slice(0, 35)
                    }}...
                  </span>
                </template>
                <span>{{ props.item.description }}</span>
              </v-tooltip>
            </template>

            <!-- Top -->
            <template v-slot:top v-if="!printMode">
              <v-btn
                color="error"
                small
                :disabled="!selectedItems.length"
                class="ma-2 text-right"
                @click="deleteMultiple"
                v-if="can('utility_delete')"
                ><v-icon left>mdi-trash-can-outline</v-icon> Delete
                Selected</v-btn
              >

              <v-btn
                color="success"
                small
                link
                to="/utilities/add"
                class="ma-2 text-right"
                v-if="can('utility_create')"
              >
                <v-icon left>mdi-account-plus-outline</v-icon>
                New Utility</v-btn
              >

              <Excel
                module="utilities"
                :ids="selectedItems.map((item) => item.id)"
              />
              <CSV
                module="utilities"
                :ids="selectedItems.map((item) => item.id)"
              />
              <PDF
                module="utilities"
                :ids="selectedItems.map((item) => item.id)"
              />

              <v-text-field
                v-model="search"
                placeholder="Search"
                class="mx-4"
                append-icon="mdi-magnify"
              ></v-text-field>
            </template>

            <!-- Actions -->
            <template slot="item.actions" slot-scope="props">
              <v-btn
                x-small
                text
                color="primary"
                :to="`/utilities/edit/${props.item.id}`"
                title="Edit"
                v-if="can('utility_edit')"
              >
                <v-icon small>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                x-small
                text
                color="red darken-2"
                @click="setUtilityId(props.item.id)"
                title="Delete"
                v-if="can('utility_delete')"
              >
                <v-icon small>mdi-delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>

          <!-- Cheque Images -->
          <v-dialog v-model="chequeImagesDialog" width="600">
            <ChequeImages
              :current-cheque-images="currentChequeImages"
              @closeDialog="closeChequeImagesDialog"
            />
          </v-dialog>

          <!-- Confirmation -->
          <Confirmation
            ref="confirmationComponent"
            :id="utilityId"
            @confirmDeletion="
              utilityId
                ? handleUtilityDelete()
                : handleMultipleUtilitiesDelete()
            "
          />
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import EditUtility from "./EditUtility.vue";
import Confirmation from "../globals/Confirmation";
import Navbar from "../navs/Navbar";
import ChequeImages from "../globals/ChequeImages.vue";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
  mixins: [DatatableMixin, CurrencyMixin],

  components: {
    EditUtility,
    Navbar,
    Confirmation,
    ChequeImages,
    Excel,
    CSV,
    PDF,
  },

  data() {
    return {
      currentChequeImages: null,
      chequeImagesDialog: false,
      headers: [
        { text: "S#", value: "sno" },
        { text: "Name", value: "name" },
        { text: "Amount", value: "amount" },
        { text: "Payment Method", value: "payment.payment_method" },
        { text: "Date", value: "payment.payment_date" },
        { text: "Bank", value: "payment.bank.name" },
        { text: "Cheque Type", value: "payment.cheque_type" },
        { text: "Cheque No.", value: "payment.cheque_no" },
        { text: "Cheque Due Date", value: "payment.cheque_due_date" },
        { text: "Cheque Images", value: "payment.cheque_images" },
        { text: "Description", value: "description" },
        { text: "Actions", value: "actions", align: " d-print-none" },
      ],
      selectedItems: [],
      utilityId: null,
      currentPhoto: null,
    };
  },

  methods: {
    ...mapActions({
      getUtilities: "utility/getUtilities",
      getUtility: "utility/getUtility",
      deleteUtility: "utility/deleteUtility",
      deleteMultipleUtilities: "utility/deleteMultipleUtilities",
    }),

    setCurrentChequeImages(images) {
      this.currentChequeImages = images;
      this.chequeImagesDialog = true;
    },

    closeChequeImagesDialog() {
      this.currentChequeImages = null;
      this.chequeImagesDialog = false;
    },

    setUtilityId(id) {
      this.utilityId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleUtilityDelete() {
      await this.deleteUtility(this.utilityId);
      this.utilityId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultipleUtilitiesDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultipleUtilities(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      utilities: "utility/utilities",
      utility: "utility/utility",
      loading: "loading",
    }),
  },

  mounted() {
    // remove actions if no access is given
    if (!this.can("utility_edit") && !this.can("utility_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }
    this.getUtilities();
  },
};
</script>