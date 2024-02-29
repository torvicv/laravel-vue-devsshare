<script>
  export default {
    mounted() {
      // Dynamically add CSRF token meta tag to the document's <head> section
      const metaTag = document.createElement('meta');
      metaTag.setAttribute('name', 'csrf-token');
      metaTag.setAttribute('content', '{{ csrf_token() }}');
      document.head.appendChild(metaTag);
    }
  }
</script>
