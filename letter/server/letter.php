<?php
// 设定PHP文件运行的端口
$port = 3000;
$host = '127.0.0.1';
$command = "php -S $host:$port -t .";
shell_exec($command);

// 接收客户端发送的JSON数据
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$birthday = $data['birthday'] ?? '';

// 读取letter.json文件
$jsonData = file_get_contents('letter.json');
$letters = json_decode($jsonData, true);

// 查找对应的姓名并验证生日
$response = ['status' => 'error', 'message' => 'Name or birthday is incorrect'];
foreach ($letters as &$letter) {
    if ($letter['name'] === $name && $letter['birthday'] === $birthday) {
        $response = ['status' => 'success', 'content' => $letter['content'], 'message' => 'Success'];
        $letter['number'] += 1; // 增加number的值
        break;
    }
}

// 如果查询不正确则返回json文件中default中的content，并将接收到的name和birthday写入json文件中
if ($response['status'] === 'error') {
    if (isset($letters['default'])) {
        $response = ['status' => 'success', 'content' => $letters['default']['content'], 'message' => 'Default'];
        $letters['default']['number'] += 1; // 增加number的值
    }
    $letters[$name] = [
        'name' => $name,
        'birthday' => $birthday,
        'number' => 0,
        'content' => '新年快乐。'
    ];
}

// 将修改后的数据写回letter.json文件
file_put_contents('letter.json', json_encode($letters));

// 返回结果
header('Content-Type: application/json');
echo json_encode($response);
?>
