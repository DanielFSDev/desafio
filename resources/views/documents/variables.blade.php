<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Variáveis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="container">
    @include('messages')
    <h2>Formulário de Variáveis</h2>
    <form action="{{ route('documents.variables') }}" method="POST">
        @csrf
        <input type="hidden" name="document_id" value="{{ $document->id }}">

        <label for="user_name">Nome do usuário:</label>
        <input type="text" id="user_name" name="variables[user_name]" value="{{ $variables['user_name'] ?? null }}" required>

        <label for="user_role">Cargo:</label>
        <input type="text" id="user_role" name="variables[user_role]" value="{{ $variables['user_role'] ?? null  }}" required>

        <label for="user_document">CPF:</label>
        <input type="text" id="user_document" name="variables[user_document]" value="{{ $variables['user_document'] ?? null  }}" required>

        <label for="product_brand">Marca do notebook:</label>
        <input type="text" id="product_brand" name="variables[product_brand]" value="{{ $variables['product_brand'] ?? null  }}" required>

        <label for="product_model">Modelo do notebook:</label>
        <input type="text" id="product_model" name="variables[product_model]" value="{{ $variables['product_model'] ?? null  }}" required>

        <label for="product_serial_number">Número de série do notebook:</label>
        <input type="text" id="product_serial_number" name="variables[product_serial_number]" value="{{ $variables['product_serial_number'] ?? null  }}" required>

        <label for="product_processor">Processador do notebook:</label>
        <input type="text" id="product_processor" name="variables[product_processor]" value="{{ $variables['product_processor'] ?? null  }}" required>

        <label for="product_memory">Memória do notebook:</label>
        <input type="text" id="product_memory" name="variables[product_memory]" value="{{ $variables['product_memory'] ?? null  }}" required>

        <label for="product_disk">Disco do notebook:</label>
        <input type="text" id="product_disk" name="variables[product_disk]" value="{{ $variables['product_disk'] ?? null  }}" required>

        <label for="product_price">Preço do notebook:</label>
        <input type="number" id="product_price" name="variables[product_price]" value="{{ $variables['product_price'] ?? null  }}" required>

        <label for="product_price_string">Preço do notebook (como String):</label>
        <input type="text" id="product_price_string" name="variables[product_price_string]" value="{{ $variables['product_price_string'] ?? null  }}" required>

        <label for="local">Local:</label>
        <input type="text" id="local" name="variables[local]" value="{{ $variables['local'] ?? null  }}" required>

        <label for="date">date:</label>
        <input type="date" id="date" name="variables[date]" value="{{ $variables['date'] ?? null  }}" required>

        <button type="submit">Salvar</button>
    </form>
</div>
</body>
</html>
