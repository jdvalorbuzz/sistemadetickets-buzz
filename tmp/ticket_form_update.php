                        Forms\Components\Select::make('department_id')
                            ->label('Departamento')
                            ->relationship('department', 'name')
                            ->options(function () {
                                // Obtener solo departamentos activos
                                return \App\Models\Department::where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(),
                                Forms\Components\TextInput::make('code')
                                    ->label('Código')
                                    ->required()
                                    ->maxLength(20)
                                    ->unique()
                                    ->alpha()
                                    ->uppercase(),
                                Forms\Components\ColorPicker::make('color')
                                    ->label('Color')
                                    ->required(),
                            ])
                            ->disabled(fn() => static::isReadOnlyForSupport()),
