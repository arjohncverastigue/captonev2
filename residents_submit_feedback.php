<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Residents') {
    header("Location: login.php");
    exit();
}

include 'conn.php';
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');

    try {
        $feedbackData = [
            'appointment_id' => $_POST['appointment_id'],
            'user_id' => $userId,
            'feedback' => trim($_POST['feedback'] ?? ''),
            'q1_office_easy_locate' => $_POST['q1_office_easy_locate'] ?? null,
            'q2_office_clean' => $_POST['q2_office_clean'] ?? null,
            'q3_office_long_line' => $_POST['q3_office_long_line'] ?? null,
            'q4_office_signs' => $_POST['q4_office_signs'] ?? null,

            'q1_staff_available' => $_POST['q1_staff_available'] ?? null,
            'q2_staff_respectful' => $_POST['q2_staff_respectful'] ?? null,
            'q3_staff_comfortable' => $_POST['q3_staff_comfortable'] ?? null,
            'q4_staff_wait_long' => $_POST['q4_staff_wait_long'] ?? null,
            'q5_staff_knowledgeable' => $_POST['q5_staff_knowledgeable'] ?? null,

            'q1_req_informed' => $_POST['q1_req_informed'] ?? null,
            'q2_req_many' => $_POST['q2_req_many'] ?? null,
            'q3_req_how_to_get' => $_POST['q3_req_how_to_get'] ?? null,
            'q4_req_fee' => $_POST['q4_req_fee'] ?? null,

            'q1_officer_present' => $_POST['q1_officer_present'] ?? null,
            'q2_officer_slow' => $_POST['q2_officer_slow'] ?? null,

            'q1_info_available' => $_POST['q1_info_available'] ?? null,
            'q2_info_complete' => $_POST['q2_info_complete'] ?? null,
            'q3_info_clear' => $_POST['q3_info_clear'] ?? null,

            'comments' => $_POST['comments'] ?? null,
            'attending_employee_name' => $_POST['attending_employee_name'] ?? null
        ];

        $queryInsertFeedback = "INSERT INTO feedback (
            appointment_id, user_id, feedback,
            q1_office_easy_locate, q2_office_clean, q3_office_long_line, q4_office_signs,
            q1_staff_available, q2_staff_respectful, q3_staff_comfortable, q4_staff_wait_long, q5_staff_knowledgeable,
            q1_req_informed, q2_req_many, q3_req_how_to_get, q4_req_fee,
            q1_officer_present, q2_officer_slow,
            q1_info_available, q2_info_complete, q3_info_clear,
            comments, attending_employee_name
        ) VALUES (
            :appointment_id, :user_id, :feedback,
            :q1_office_easy_locate, :q2_office_clean, :q3_office_long_line, :q4_office_signs,
            :q1_staff_available, :q2_staff_respectful, :q3_staff_comfortable, :q4_staff_wait_long, :q5_staff_knowledgeable,
            :q1_req_informed, :q2_req_many, :q3_req_how_to_get, :q4_req_fee,
            :q1_officer_present, :q2_officer_slow,
            :q1_info_available, :q2_info_complete, :q3_info_clear,
            :comments, :attending_employee_name
        )";

        $stmtInsert = $pdo->prepare($queryInsertFeedback);
        $stmtInsert->execute($feedbackData);

        $stmtUpdate = $pdo->prepare("UPDATE appointments SET feedback_status = 'done' WHERE id = :appointment_id");
        $stmtUpdate->execute(['appointment_id' => $_POST['appointment_id']]);

        echo json_encode(['success' => true, 'message' => 'Feedback submitted successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
    exit();
}

$queryCompletedAppointments = "
    SELECT a.id AS appointment_id, d.name AS department_name, a.scheduled_for
    FROM appointments a
    JOIN departments d ON a.department_id = d.id
    WHERE a.user_id = :user_id 
      AND a.status = 'Completed'
      AND a.feedback_status = 'pending'
";
$stmt = $pdo->prepare($queryCompletedAppointments);
$stmt->execute(['user_id' => $userId]);
$completedAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">
<<<<<<< Updated upstream
<div class="container">
    <h3>Submit Feedback</h3>
    <button type="button" class="btn btn-secondary mb-3" onclick="$('#content-area').load('residents_select_form.php')">← Back to Form Selector</button>
=======
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="text-primary mb-4"><i class="fas fa-comment-dots mr-2"></i>Submit Your Feedback</h3>
>>>>>>> Stashed changes

            <button type="button" class="btn btn-outline-secondary mb-4" onclick="$('#content-area').load('residents_select_form.php')">
                ← Back to Form Selector
            </button>

            <div id="alert-container"></div>

            <?php if (!empty($completedAppointments)): ?>
            <form id="feedback-form">
                <div class="form-group">
                    <label for="appointment_id"><strong>Select Completed Appointment:</strong></label>
                    <select class="form-control border-primary" id="appointment_id" name="appointment_id" required>
                        <option value="">-- Select Appointment --</option>
                        <?php foreach ($completedAppointments as $appointment): ?>
                            <option value="<?= $appointment['appointment_id']; ?>">
                                <?= htmlspecialchars($appointment['department_name']) . " - " . date('F d, Y h:i A', strtotime($appointment['scheduled_for'])); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="alert alert-info">Please answer the following questions:</div>

                <!-- Structured Sections -->
                <h5 class="text-muted mt-4 mb-2">A. About the Office</h5>
                <?php
                $section_breaks = [
                    'q1_office_easy_locate' => 'A. About the Office',
                    'q1_staff_available' => 'B. About the Staff',
                    'q1_req_informed' => 'C. About the Requirements',
                    'q1_officer_present' => 'D. About the Officers',
                    'q1_info_available' => 'E. About the Documents/Information',
                ];

                $questions = [
                    'q1_office_easy_locate' => '1. Was it easy to locate the office?',
                    'q2_office_clean' => '2. Was it clean and orderly?',
                    'q3_office_long_line' => '3. Is there a long line of clients?',
                    'q4_office_signs' => '4. Did you find proper directional signs/instructions?',
                    'q1_staff_available' => '1. Were the designated employees available?',
                    'q2_staff_respectful' => '2. Were they respectful?',
                    'q3_staff_comfortable' => '3. Did they make you feel comfortable?',
                    'q4_staff_wait_long' => '4. Did they make you wait long?',
                    'q5_staff_knowledgeable' => '5. Were they knowledgeable?',
                    'q1_req_informed' => '1. Were you properly informed what you needed to present?',
                    'q2_req_many' => '2. Were there many requirements?',
                    'q3_req_how_to_get' => '3. Were you informed how to get the requirements?',
                    'q4_req_fee' => '4. Were you made aware of how much you will have to pay?',
                    'q1_officer_present' => '1. Were the authorized officials present?',
                    'q2_officer_slow' => '2. Did it take them long to sign the documents?',
                    'q1_info_available' => '1. Was the document you needed available?',
                    'q2_info_complete' => '2. Was the data complete?',
                    'q3_info_clear' => '3. Were the instructions clear and short?'
                ];

                $current_section = '';
                foreach ($questions as $name => $label):
                    if (isset($section_breaks[$name]) && $section_breaks[$name] !== $current_section):
                        $current_section = $section_breaks[$name];
                        echo "<h5 class='text-muted mt-4 mb-2'>{$current_section}</h5>";
                    endif;
                ?>
                    <div class="form-group">
                        <label><?= $label ?></label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="<?= $name ?>" value="1" required>
                            <label class="form-check-label">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="<?= $name ?>" value="0">
                            <label class="form-check-label">No</label>
                        </div>
                    </div>
                <?php endforeach; ?>

<<<<<<< Updated upstream
        <div class="alert alert-info">Please answer the following:</div>

        <!-- Section: THE OFFICE -->
        <h5 class="mt-4">THE OFFICE</h5>
        <?php
        $office_questions = [
            'q1_office_easy_locate' => '1. Was it easy to locate the office?',
            'q2_office_clean' => '2. Was it clean and orderly?',
            'q3_office_long_line' => '3. Is there a long line of clients?',
            'q4_office_signs' => '4. Did you find proper directional signs/instructions?'
        ];
        foreach ($office_questions as $name => $label): ?>
            <div class="form-group">
                <label><?= $label ?></label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="<?= $name ?>" value="1" required> Yes
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="<?= $name ?>" value="0"> No
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Section: THE PERSONS RESPONSIBLE -->
        <h5 class="mt-4">THE PERSONS RESPONSIBLE</h5>
        <?php
        $staff_questions = [
            'q1_staff_available' => '1. Were the designated employees available?',
            'q2_staff_respectful' => '2. Were they respectful?',
            'q3_staff_comfortable' => '3. Did they make you feel comfortable?',
            'q4_staff_wait_long' => '4. Did they make you wait long?',
            'q5_staff_knowledgeable' => '5. Were they knowledgeable?'
        ];
        foreach ($staff_questions as $name => $label): ?>
            <div class="form-group">
                <label><?= $label ?></label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="<?= $name ?>" value="1" required> Yes
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="<?= $name ?>" value="0"> No
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Section: THE REQUIREMENTS -->
        <h5 class="mt-4">THE REQUIREMENTS</h5>
        <?php
        $requirement_questions = [
            'q1_req_informed' => '1. Were you properly informed what you needed to present?',
            'q2_req_many' => '2. Were there many requirements?',
            'q3_req_how_to_get' => '3. Were you informed how to get the requirements?',
            'q4_req_fee' => '4. Were you made aware of how much you will have to pay?'
        ];
        foreach ($requirement_questions as $name => $label): ?>
            <div class="form-group">
                <label><?= $label ?></label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="<?= $name ?>" value="1" required> Yes
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="<?= $name ?>" value="0"> No
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Section: THE OFFICERS -->
        <h5 class="mt-4">THE OFFICERS</h5>
        <?php
        $officer_questions = [
            'q1_officer_present' => '1. Were the authorized officials present?',
            'q2_officer_slow' => '2. Did it take them long to sign the documents?'
        ];
        foreach ($officer_questions as $name => $label): ?>
            <div class="form-group">
                <label><?= $label ?></label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="<?= $name ?>" value="1" required> Yes
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="<?= $name ?>" value="0"> No
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Section: THE INFORMATION -->
        <h5 class="mt-4">THE INFORMATION</h5>
        <?php
        $info_questions = [
            'q1_info_available' => '1. Was the document you needed available?',
            'q2_info_complete' => '2. Was the data complete?',
            'q3_info_clear' => '3. Were the instructions clear and short?'
        ];
        foreach ($info_questions as $name => $label): ?>
            <div class="form-group">
                <label><?= $label ?></label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="<?= $name ?>" value="1" required> Yes
=======
                <!-- Text Feedback -->
                <div class="form-group mt-4">
                    <label for="comments"><strong>Other Comments and Suggestions:</strong></label>
                    <textarea class="form-control" name="comments" id="comments" rows="3" placeholder="Write any additional feedback here..."></textarea>
>>>>>>> Stashed changes
                </div>

                <div class="form-group">
                    <label for="attending_employee_name"><strong>Name of Attending Employee (Optional):</strong></label>
                    <input type="text" class="form-control" name="attending_employee_name" id="attending_employee_name">
                </div>

                <div class="form-group">
                    <label for="feedback"><strong>Additional Feedback:</strong></label>
                    <textarea class="form-control" name="feedback" id="feedback" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg mt-2">Submit Feedback</button>
            </form>

            <?php else: ?>
                <div class="alert alert-warning">You have no completed appointments to provide feedback for.</div>
            <?php endif; ?>
        </div>
    </div>
</div>


<script>
    $('#feedback-form').on('submit', function (e) {
        e.preventDefault();

        if (!$('#appointment_id').val()) {
            alert('Please select an appointment.');
            return;
        }

        $('button[type="submit"]').prop('disabled', true);
        $.ajax({
            url: 'residents_submit_feedback.php',
            type: 'POST',
            data: $('#feedback-form').serialize() + '&ajax=true',
            success: function (response) {
                $('button[type="submit"]').prop('disabled', false);
                if (response.success) {
                    $('#alert-container').html('<div class="alert alert-success">' + response.message + '</div>');
                    $('#feedback-form')[0].reset();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    $('#alert-container').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function () {
                $('button[type="submit"]').prop('disabled', false);
                $('#alert-container').html('<div class="alert alert-danger">An unexpected error occurred.</div>');
            }
        });
    });
</script>

</body>
</html>
