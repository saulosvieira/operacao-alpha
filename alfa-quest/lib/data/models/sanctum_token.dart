import 'package:freezed_annotation/freezed_annotation.dart';

part 'sanctum_token.freezed.dart';
part 'sanctum_token.g.dart';

@freezed
abstract class SanctumToken with _$SanctumToken {
  const factory SanctumToken({
    required String value,
    @JsonKey(name: 'obtained_at') required DateTime obtainedAt,
    @JsonKey(name: 'persist_across_launches')
    @Default(false)
    bool persistAcrossLaunches,
  }) = _SanctumToken;

  factory SanctumToken.fromJson(Map<String, dynamic> json) =>
      _$SanctumTokenFromJson(json);
}
