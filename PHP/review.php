<?php
if (isset($_POST['submit_review'])) {
    $user_id = $_SESSION['user_id'];  // make sure user is logged in
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    try {
        $pdo = new PDO('mysql:host=localhost;dbname=e-shopmanage', 'root', '');

        $stmt = $pdo->prepare("CALL InsertReview(:user_id, :product_id, :rating, :comment)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

        $stmt->execute();

        echo "<p>✅ Review submitted successfully!</p>";
    } catch (PDOException $e) {
        echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Review Form</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 350px;
        }

        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333333;
        }

        .product-name {
            margin-bottom: 15px;
            font-size: 16px;
            color: #555555;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #444444;
            font-weight: bold;
        }

        select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 6px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        .submit-btn {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1>Submit Review</h1>
    <form method="post" action="UserDashboard.php" class="review-form" id="reviewForm">
        <input type="hidden" name="user_id" value="123">
        <input type="hidden" name="product_id" value="456">

        <div class="product-name">
            <strong>Product:</strong> <span>Example Product</span>
        </div>

        <label for="rating">Rating (1-5 stars):</label>
        <select name="rating" id="rating" required>
            <option value="1">⭐</option>
            <option value="2">⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="5">⭐⭐⭐⭐⭐</option>
        </select>

        <label for="comment">Your Review:</label>
        <textarea name="comment" id="comment" rows="5" placeholder="Write your review here..."></textarea>

        <button type="submit" name="submit_review" class="submit-btn">Submit Review</button>
    </form>
</div>

<script>
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const rating = document.getElementById('rating').value;
        const commentField = document.getElementById('comment');
        let comment = commentField.value.trim();

        function showSummaryAndSubmit() {
            let summaryHtml = `<p><strong>Rating:</strong> ${'⭐'.repeat(rating)}</p>`;
            if (comment !== "") {
                summaryHtml += `<p>${comment}</p>`;
            }

            Swal.fire({
                title: 'Review Summary',
                html: summaryHtml,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Edit'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Thank you!',
                        text: 'Your review has been submitted successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        e.target.submit();
                    });
                }
            });
        }

        if (comment === "") {
            Swal.fire({
                title: 'Do you want to add a comment?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    commentField.focus();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    showSummaryAndSubmit();
                }
            });
        } else {
            showSummaryAndSubmit();
        }
    });
</script>

</body>
</html>