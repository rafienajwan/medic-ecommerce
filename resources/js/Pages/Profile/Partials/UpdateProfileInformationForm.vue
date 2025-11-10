<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone || '',
    address: props.user.address || '',
    city: props.user.city || '',
    province: props.user.province || '',
    postal_code: props.user.postal_code || '',
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Profile Information
        </template>

        <template #description>
            Update your account's profile information and email address.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input
                    id="photo"
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                >

                <InputLabel for="photo" value="Photo" />

                <!-- Current Profile Photo -->
                <div v-show="! photoPreview" class="mt-2">
                    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full size-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                        :style="'background-image: url(\'' + photoPreview + '\');'"
                    />
                </div>

                <SecondaryButton class="mt-2 me-2" type="button" @click.prevent="selectNewPhoto">
                    Select A New Photo
                </SecondaryButton>

                <SecondaryButton
                    v-if="user.profile_photo_path"
                    type="button"
                    class="mt-2"
                    @click.prevent="deletePhoto"
                >
                    Remove Photo
                </SecondaryButton>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />
                <InputError :message="form.errors.email" class="mt-2" />

                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="text-sm mt-2">
                        Your email address is unverified.

                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            @click.prevent="sendEmailVerification"
                        >
                            Click here to re-send the verification email.
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
                        A new verification link has been sent to your email address.
                    </div>
                </div>
            </div>

            <!-- Phone -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="phone" value="Phone Number" />
                <TextInput
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="mt-1 block w-full"
                    autocomplete="tel"
                    placeholder="08123456789"
                />
                <InputError :message="form.errors.phone" class="mt-2" />
            </div>

            <!-- Address -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="address" value="Address" />
                <textarea
                    id="address"
                    v-model="form.address"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                    rows="2"
                    autocomplete="street-address"
                    placeholder="Jl. Contoh No. 123"
                ></textarea>
                <InputError :message="form.errors.address" class="mt-2" />
            </div>

            <!-- City & Province -->
            <div class="col-span-6 sm:col-span-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="city" value="City" />
                        <TextInput
                            id="city"
                            v-model="form.city"
                            type="text"
                            class="mt-1 block w-full"
                            autocomplete="address-level2"
                            placeholder="Jakarta"
                        />
                        <InputError :message="form.errors.city" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="province" value="Province" />
                        <TextInput
                            id="province"
                            v-model="form.province"
                            type="text"
                            class="mt-1 block w-full"
                            autocomplete="address-level1"
                            placeholder="DKI Jakarta"
                        />
                        <InputError :message="form.errors.province" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Postal Code -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="postal_code" value="Postal Code" />
                <TextInput
                    id="postal_code"
                    v-model="form.postal_code"
                    type="text"
                    class="mt-1 block w-full"
                    maxlength="5"
                    autocomplete="postal-code"
                    placeholder="12345"
                />
                <InputError :message="form.errors.postal_code" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
