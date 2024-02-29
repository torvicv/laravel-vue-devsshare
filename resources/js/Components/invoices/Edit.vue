<script setup>

    import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

    let form = ref({
        id: ''
    });

    let customers = ref([]);
    const showModal = ref(false);
    const hideModal = ref(true);
    let listProducts = ref([]);
    const router = useRouter();

    const props = defineProps({
        id: {
            type: String,
            default: ''
        }
    });

    onMounted( async () => {
        get_invoice();
        get_all_customers();
        getProducts();
    });

    const get_invoice = async () => {
        let response = await axios.get(`/api/get_invoice/${props.id}`);
        console.log(response);
        form.value = response.data.invoice;
    }

    const get_all_customers = async () => {
        let response = await axios.get(`/api/customers`);
        console.log(response);
        customers.value = response.data.customers;
    }

    const deleteInvoiceItem = (id, i) => {
        form.value.invoice_items.splice(i, 1);
        if (id != undefined) {
            axios.get('/api/delete_invoice_items/'+id);
        }
    }

    const openModal = () => {
        showModal.value = !showModal.value;
    }

    const closeModal = () => {
        showModal.value = !hideModal.value;
    }

    const getProducts = async () => {
        let response = await axios.get('/api/products');
        console.log(response.data.products);
        listProducts.value = response.data.products;
    }

    const addCart = (item) => {
        const itemCart = {
            product_id: item.id,
            product: {
                item_code: item.item_code,
                unit_price: item.unit_price,
                description: item.description
            },
            unit_price: item.unit_price,
            quantity: item.quantity
        };

        form.value.invoice_items.push(itemCart);
        closeModal();
    }

    const subTotal = () => {
        let total = 0;
        if (form.value.invoice_items && form.value.invoice_items.length > 0) {
            form.value.invoice_items.map((data) => {
                total = total + (data.quantity*data.unit_price);
            });
        }
        return total;
    }

    const totalMount = () => {
        if (form.value.invoice_items && form.value.invoice_items.length > 0)
            return subTotal() - form.value.discount;
    }

    const onEdit = (id) => {
        if (form.value.invoice_items && form.value.invoice_items.length > 0) {
            let subtotal = 0;
            subtotal = subTotal();

            let $total = 0;
            $total = totalMount();

            const formData = new FormData();
            formData.append('invoice_item', JSON.stringify(form.value.invoice_items));
            formData.append('customer_id', form.value.customer_id);
            formData.append('date', form.value.date);
            formData.append('due_date', form.value.due_date);
            formData.append('number', form.value.number);
            formData.append('reference', form.value.reference);
            formData.append('discount', form.value.discount);
            formData.append('sub_total', parseInt(subtotal));
            formData.append('total', parseInt($total));
            formData.append('terms_and_conditions', form.value.terms_and_conditions);

            axios.post('/api/update_invoice/'+form.value.id, formData);

            form.value.invoice_items = [];

            router.push('/');
        }
    }

</script>

<template>
    <div class="invoices">

        <div class="card__header">
            <div>
                <h2 class="invoice__title">Edit Invoice</h2>
            </div>
            <div>

            </div>
        </div>

        <div class="card__content">
            <div class="card__content--header">
                <div>
                    <p class="my-1">Customer</p>
                    <select name="" id="" class="input" v-model="form.customer_id">
                        <option disabled></option>
                        <option v-for="customer in customers" :value="customer.id" :key="customer.id">
                            {{ customer.firstname }}
                        </option>
                    </select>
                </div>
                <div>
                    <p class="my-1">Date</p>
                    <input id="date" placeholder="dd-mm-yyyy" type="date" class="input" v-model="form.date"> <!---->
                    <p class="my-1">Due Date</p>
                    <input id="due_date" type="date" class="input" v-model="form.due_date">
                </div>
                <div>
                    <p class="my-1">Numero</p>
                    <input type="text" class="input" v-model="form.number">
                    <p class="my-1">Reference(Optional)</p>
                    <input type="text" class="input" v-model="form.reference">
                </div>
            </div>
            <br><br>
            <div class="table">

                <div class="table--heading2">
                    <p>Item Description</p>
                    <p>Unit Price</p>
                    <p>Qty</p>
                    <p>Total</p>
                    <p></p>
                </div>

                <!-- item 1 -->
                <div class="table--items2" v-if="form.invoice_items" v-for="(item, i) in form.invoice_items">
                    <p>{{ item.product.item_code }} {{ item.product.description }} </p>
                    <p>
                        <input type="text" class="input" v-model="item.product.unit_price" >
                    </p>
                    <p>
                        <input type="text" class="input" v-model="item.quantity" >
                    </p>
                    <p>
                        {{ item.product.unit_price * item.quantity }}
                    </p>
                    <p style="color: red; font-size: 24px;cursor: pointer;" @click="deleteInvoiceItem(item.id, i)">
                        &times;
                    </p>
                </div>
                <div style="padding: 10px 30px !important;">
                    <button class="btn btn-sm btn__open--modal" @click="openModal()">Add New Line</button>
                </div>
            </div>

            <div class="table__footer">
                <div class="document-footer" >
                    <p>Terms and Conditions</p>
                    <textarea cols="50" rows="7" class="textarea" v-model="form.terms_and_conditions">
                        {{ form.terms_and_conditions }}
                    </textarea>
                </div>
                <div>
                    <div class="table__footer--subtotal">
                        <p>Sub Total</p>
                        <span>$ {{ subTotal() }}</span>
                    </div>
                    <div class="table__footer--discount">
                        <p>Discount</p>
                        <input type="text" class="input" v-model="form.discount">
                    </div>
                    <div class="table__footer--total">
                        <p>Grand Total</p>
                        <span>$ {{ totalMount() }}</span>
                    </div>
                </div>
            </div>


        </div>
        <div class="card__header" style="margin-top: 40px;">
            <div>

            </div>
            <div>
                <a class="btn btn-secondary" @click="onEdit(form.id)">
                    Save
                </a>
            </div>
        </div>

    </div>
    <!--==================== add modal items ====================-->
    <div class="modal main__modal " :class="{ show: showModal}" v-if="showModal">
        <div class="modal__content">
            <span class="modal__close btn__close--modal" @click="closeModal()">×</span>
            <h3 class="modal__title">Add Item</h3>
            <hr><br>
            <div class="modal__items">
                <ul>
                    <li v-for="(item, i) in listProducts" :key="item.id" style="display: grid; grid-template-columns: 30px 350px 15px; align-items: center; padding-bottom: 5px;;">
                        <p>{{ i+1 }}</p>
                        <a href="#">{{ item.item_code }} {{ item.description }}</a>
                        <button @click="addCart(item)" style="border: 1px solid #e0e0e0; width: 35px; cursor: pointer;">
                            +
                        </button>
                    </li>
                </ul>
            </div>
            <br><hr>
            <div class="model__footer">
                <button class="btn btn-light mr-2 btn__close--modal" @click="closeModal()">
                    Cancel
                </button>
                <button class="btn btn-light btn__close--modal ">Save</button>
            </div>
        </div>
    </div>

    <br><br><br>
</template>
