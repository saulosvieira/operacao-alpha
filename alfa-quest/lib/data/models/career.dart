import 'package:freezed_annotation/freezed_annotation.dart';

part 'career.freezed.dart';
part 'career.g.dart';

@freezed
abstract class Career with _$Career {
  const factory Career({
    @JsonKey(fromJson: _idFromJson) required int id,
    required String name,
  }) = _Career;

  factory Career.fromJson(Map<String, dynamic> json) => _$CareerFromJson(json);
}

int _idFromJson(dynamic value) {
  if (value is int) return value;
  if (value is String) return int.parse(value);
  return 0;
}
