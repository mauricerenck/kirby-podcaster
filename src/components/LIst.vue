<template>
    <ul class="podcasterList">
        <li
            v-for="(entry, index) in this.items"
            :key="index"
            text="Some image from unsplash"
            info="random-unsplash.jpg"
        >
            <k-grid>
                <k-column width="8/12">
                    {{ entry.text }}
                </k-column>
                <k-column width="4/12" class="downloads">
                    {{ entry.downloads }}
                </k-column>
            </k-grid>
        </li>
    </ul>
</template>

<script>
export default {
    props: {
        items: Array,
    },
}
</script>

<style lang="scss">
.podcasterList {
    transition: all 0.125s;
    border: 1px solid var(--color-border);
    border-radius: var(--rounded);
    box-shadow: var(--shadow);
    background-color: white;

    li {
        border-bottom: 1px solid var(--color-border);
        padding: 10px;
    }

    .downloads {
        color: var(--color-gray);
        text-align: right;
    }
}
</style>
