<script setup>
    import Select from 'primevue/select'

    import {
        FwbAccordion,
        FwbAccordionPanel,
        FwbAccordionHeader,
        FwbAccordionContent,
    } from 'flowbite-vue'

    import { ref, computed, watch, onMounted } from 'vue'
    import { formatDate } from '@/helpers/formatter'

    const props = defineProps({
        test_categories: Array,
        appointment_schedules: Array,
        form: Object,
    })

    // Formated appointment schedules
    const formattedSchedules = computed(() => {
        return props.appointment_schedules.map((s) => ({
            id: s.id,
            schedule: formatDate(new Date(s.schedule), 'MMMM dd, yyyy hh:mm a'),
        }))
    })

    // Store selected test type IDs
    const selectedTypeIds = ref([])

    // Flatten all test types to look up prices easily
    const allTestTypes = computed(() =>
        props.test_categories.flatMap((category) => category.test_types || []),
    )

    // Compute total price from selected type IDs
    const totalPrice = computed(() => {
        return selectedTypeIds.value.reduce((total, id) => {
            const type = allTestTypes.value.find((t) => t.id === id)
            return type ? total + Number(type.price) : total
        }, 0)
    })

    // Watch every new test types checked, to be inserted in form data
    watch(selectedTypeIds, (newVal) => {
        props.form.selected_type_ids = newVal
    })

    onMounted(() => {
        console.log('appointment_schedules: ', props.appointment_schedules)
    })
</script>

<template>
    <fwb-accordion class="p-5">
        <div class="flex justify-between items-center mb-8">
            <div class="flex flex-col">
                <h1 class="text-3xl font-bold">Test Type and Schedule</h1>
                <p class="text-gray-500 text-sm">Please choose type according to your needs</p>
            </div>

            <div class="w-1/4">
                <label for="gender" class="block text-sm text-gray-900">Pick a Schedule</label>

                <Select
                    v-model="form.selected_schedule"
                    :options="formattedSchedules"
                    optionLabel="schedule"
                    optionValue="id"
                    class="w-full"
                />
            </div>
        </div>

        <fwb-accordion-panel v-for="category in test_categories" :key="category.id">
            <fwb-accordion-header>
                {{ category.name }}
            </fwb-accordion-header>
            <fwb-accordion-content>
                <div v-if="category.test_types && category.test_types.length">
                    <div
                        v-for="type in category.test_types"
                        :key="type.id"
                        class="flex items-center mb-2"
                    >
                        <input
                            type="checkbox"
                            :id="'type-' + type.id"
                            :value="type.id"
                            v-model="selectedTypeIds"
                            class="mr-2"
                        />
                        <label :for="'type-' + type.id" class="text-gray-700">
                            {{ type.name }} — ₱{{ type.price }}
                        </label>
                    </div>
                </div>
                <p v-else class="text-sm text-gray-500 italic">No test types available.</p>
            </fwb-accordion-content>
        </fwb-accordion-panel>

        <!-- Total Price Display -->
        <div class="mt-4 text-right font-semibold text-lg text-green-700">
            Total Price: ₱{{ totalPrice }}
        </div>
    </fwb-accordion>
</template>
