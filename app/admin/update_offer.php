<?php
include '../required/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $offer_id = $_POST['offer_id'];
    $name = $_POST['name'];
    $bonus_title = $_POST['bonus_title'];
    $button_text = $_POST['button_text'];
    $button_text2 = $_POST['button_text2'];
    $url = $_POST['url'];
    $url2 = $_POST['url2'];
    $status = isset($_POST['status']) ? 1 : 0;

    // Fetch current image name
    $stmt = $conn->prepare("SELECT image FROM offers WHERE id = ?");
    $stmt->bind_param("i", $offer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $offer = $result->fetch_assoc();
    $old_image = $offer['image'] ?? '';

    $image_path = $old_image; // Default: Keep old image

    // Image Handling
    if (!empty($_FILES["image"]["name"])) {
        $original_name = basename($_FILES["image"]["name"]); // Original file name
        $image_extension = pathinfo($original_name, PATHINFO_EXTENSION); // Get file extension
        $target_path = "../image/" . $original_name; // Target file path

        // Check if file already exists, then rename
        if (file_exists($target_path)) {
            $random_number = rand(1000, 9999);
            $new_name = pathinfo($original_name, PATHINFO_FILENAME) . "_$random_number.$image_extension";
            $target_path = "../image/" . $new_name;
        } else {
            $new_name = $original_name;
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)) {
            // Delete old image if new one is uploaded
            if (!empty($old_image) && file_exists("../" . $old_image)) {
                unlink("../" . $old_image);
            }
            $image_path = "../image/" . $new_name; // Save new image path
        } else {
            echo "<script>alert('Image Upload Failed'); window.history.back();</script>";
            exit();
        }
    }

    // Update query (Now with correct bind_param)
    $stmt = $conn->prepare("UPDATE offers SET name=?, bonus_title=?, button_text=?, button_text2=?, url=?, url2=?, status=?, image=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $name, $bonus_title, $button_text, $button_text2, $url, $url2, $status, $image_path, $offer_id);

    if ($stmt->execute()) {
        echo "<script>alert('Offer Updated Successfully'); window.location.href='manage-campaign.php';</script>";
    } else {
        echo "<script>alert('Error Updating Offer: " . $stmt->error . "');</script>";
    }
}
?>
