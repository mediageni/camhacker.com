<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <!-- Start the form here -->
    <form method="GET" action="/search">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Search</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php include 'includes/searchform.php'; ?>
        </div>
        <!-- Keep the modal-footer class -->
        <div class="modal-footer">
          <!-- Full-width button -->
          <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
      </div>
    </form>
    <!-- End the form -->
  </div>
</div>
