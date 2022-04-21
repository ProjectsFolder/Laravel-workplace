<html lang="ru">
    <header>
        <style type="text/css">
            td {
                font-family: "calibri", monospace;
            }
        </style>
    </header>
    <body>
        <table style="width: 600px; max-width: 600px; margin: 0 auto; border: 20px solid #bbbbbb; text-align: center;">
            <tbody>
            <tr>
                <td style="height: 20px; font-size: 20px;" colspan="2">
                    Данные пользователя
                </td>
            </tr>
            <tr>
                <td style="height: 20px; font-size: 20px;" colspan="2">
                    Логин: <span style="font-weight: 900;">{{ $name }}</span>
                </td>
            </tr>
            <tr>
                <td style="height: 20px; font-size: 20px;" colspan="2">
                    Роли:
                    @foreach ($roles as $role)
                        <span style="font-weight: 900;">{{ $role }}</span>
                    @endforeach
                </td>
            </tr>
            </tbody>
        </table>
    </body>
</html>
