<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="purchase_items"
      class="elevation-1"
      item-key="id"
      :search="search"
      :items-per-page="perPage"
      :loading="loading"
      :show-select="can('purchase_item_delete') && !printMode"
      loading-text="Loading purchase items..."
      :footer-props="footerProps"
      v-model="selectedItems"
    >
      <!-- S# -->
      <template slot="item.sno" slot-scope="props">{{
        props.index + 1
      }}</template>

      <!-- Top -->
      <template v-slot:top v-if="!printMode">
        <v-btn
          color="error"
          small
          :disabled="!selectedItems.length"
          class="ma-2 text-right"
          @click="deleteMultiple"
          v-if="can('purchase_item_delete')"
          ><v-icon left>mdi-trash-can-outline</v-icon> Delete Selected</v-btn
        >

        <Excel module="purchase_items" :ids="selectedItems.map((item) => item.id)" />
        <CSV module="purchase_items" :ids="selectedItems.map((item) => item.id)" />
        <PDF module="purchase_items" :ids="selectedItems.map((item) => item.id)" />

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
          @click="showEditDialog(props.item.id)"
          title="Edit"
          v-if="can('purchase_item_edit')"
        >
          <v-icon small>mdi-pencil</v-icon>
        </v-btn>
        <v-btn
          x-small
          text
          color="red darken-2"
          @click="setPurchaseItemId(props.item.id)"
          title="Delete"
          v-if="can('purchase_item_delete')"
        >
          <v-icon small>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <!-- Edit dialog -->
    <v-dialog v-model="editDialog" max-width="500" persistent>
      <EditPurchaseItem
        :single-purchase-item="purchase_item"
        v-if="purchase_item"
        @closeDialog="closeEditDialog"
      />
    </v-dialog>

    <!-- Confirmation -->
    <Confirmation
      ref="confirmationComponent"
      :id="purchaseItemId"
      @confirmDeletion="
        purchaseItemId ? handlePurchaseItemDelete() : handleMultiplePurchaseItemsDelete()
      "
    />
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import DatatableMixin from "../../mixins/DatatableMixin";
import EditPurchaseItem from "./EditPurchaseItem.vue";
import Confirmation from "../globals/Confirmation";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";
import CurrencyMixin from "../../mixins/CurrencyMixin";

export default {
  mixins: [DatatableMixin, CurrencyMixin],

  components: {
    EditPurchaseItem,
    Confirmation,
    Excel,
    CSV,
    PDF,
  },

  data() {
    return {
      editDialog: false,
      paymentSetting: null,
      headers: [
        { text: "S#", value: "sno" },
        { text: "Purchase Item Name", value: "name" },
        { text: "Actions", value: "actions", align: " d-print-none" },
      ],
      selectedItems: [],

      purchaseItemId: null,
    };
  },

  methods: {
    ...mapActions({
      getPurchaseItems: "purchase_item/getPurchaseItems",
      getPurchaseItem: "purchase_item/getPurchaseItem",
      deletePurchaseItem: "purchase_item/deletePurchaseItem",
      deleteMultiplePurchaseItems: "purchase_item/deleteMultiplePurchaseItems",
    }),

    showEditDialog(id) {
      this.editDialog = true;

      this.getPurchaseItem(id);
    },

    closeEditDialog() {
      this.editDialog = false;
    },

    setPurchaseItemId(id) {
      this.purchaseItemId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handlePurchaseItemDelete() {
      await this.deletePurchaseItem(this.purchaseItemId);
      this.purchaseItemId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },

    async deleteMultiple() {
      this.$refs.confirmationComponent.setDialog(true);
    },

    async handleMultiplePurchaseItemsDelete() {
      const ids = this.selectedItems.map((selectedItem) => selectedItem.id);
      await this.deleteMultiplePurchaseItems(ids);

      this.$refs.confirmationComponent.setDialog(false);
      this.selectedItems = [];
    },
  },

  computed: {
    ...mapGetters({
      purchase_items: "purchase_item/purchase_items",
      purchase_item: "purchase_item/purchase_item",
      loading: "loading",
    }),
  },

  mounted() {
    // remove actions if no access is given
    if (!this.can("purchase_item_edit") && !this.can("purchase_item_delete")) {
      this.headers = this.headers.filter(
        (header) => header.value !== "actions"
      );
    }

    this.getPurchaseItems();
  },
};
</script>