<?php require \Helpers\get_fragment_path('__header'); ?>

<h1 id="title">Управление пользователями</h1>

<div class="users-container" style="max-width: 800px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 20px;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #e8f0e8; border-radius: 10px;">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Имя</th>
                <th style="padding: 12px;">Email</th>
                <th style="padding: 12px;">Роль</th>
                <th style="padding: 12px;">Действие</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) { ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;"><?php echo $user['id']; ?></td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($user['name']); ?></td>
                    <td style="padding: 10px;"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td style="padding: 10px;"><?php echo !empty($user['admin']) ? 'Админ' : 'Пользователь'; ?></td>
                    <td style="padding: 10px;">
                        <?php if ($__current_user['name'] != $user['name']) { ?>
                            <a href="/admin/users/<?php echo $user['name']; ?>/delete" class="btn" style="background: #ff6b6b; color: #fff; padding: 4px 12px; border-radius: 20px; text-decoration: none; font-size: 12px;">Удалить</a>
                        <?php } else { ?>
                            <span style="color: #999; font-size: 12px;">Это вы</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="cats">
    <a href="/" class="cat-item" id="btn">На главную</a>
</div>

<?php require \Helpers\get_fragment_path('__footer'); ?>